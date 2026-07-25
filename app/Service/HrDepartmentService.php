<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\HrDepartmentError;
use App\Enums\HrDeptStatus;
use App\Enums\HrLeaderRoleType;
use App\Exceptions\BusinessException;
use App\Models\HrDepartment;
use App\Models\HrDeptLeader;
use App\Models\HrUserDeptPost;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HrDepartmentService
{
    public function getTree(?string $keyword = null): array
    {
        $baseQuery = HrDepartment::query()->orderByDesc('dept_sort')->orderBy('id');

        if ($keyword !== null && $keyword !== '') {
            $matchedIds = (clone $baseQuery)
                ->where(function ($q) use ($keyword) {
                    $q->where('dept_name', 'like', '%'.$keyword.'%')
                        ->orWhere('dept_code', 'like', '%'.$keyword.'%');
                })
                ->pluck('id')
                ->all();

            if ($matchedIds === []) {
                return [];
            }

            $all = $baseQuery->get();
            $keepIds = $this->collectAncestorIds($all, $matchedIds);

            return $this->buildTree($all->whereIn('id', $keepIds)->values());
        }

        return $this->buildTree($baseQuery->get());
    }

    public function create(array $data): HrDepartment
    {
        $parentId = $this->normalizeParentId($data['parent_id'] ?? 0);
        $parent = $this->assertParentExists($parentId);

        $deptName = $data['dept_name'];
        $deptCode = $data['dept_code'];
        $this->assertNameUnique($deptName, $parentId);
        $this->assertCodeUnique($deptCode);

        $leaderUserId = $this->normalizeId($data['leader_user_id'] ?? 0);
        $this->assertLeaderUser($leaderUserId);

        $level = $parent ? ((int) $parent->dept_level + 1) : 1;
        $ancestors = $parent
            ? trim($parent->ancestors.','.$parent->id, ',')
            : '0';

        return HrDepartment::query()->create([
            'parent_id' => $parentId,
            'dept_name' => $deptName,
            'dept_code' => $deptCode,
            'ancestors' => $ancestors,
            'dept_level' => $level,
            'leader_user_id' => $leaderUserId,
            'dept_phone' => (string) ($data['dept_phone'] ?? ''),
            'dept_sort' => (int) ($data['dept_sort'] ?? 0),
            'dept_status' => HrDeptStatus::from((int) ($data['dept_status'] ?? HrDeptStatus::Enabled->value)),
            'created_by' => Auth::guard('backend')->id() ?? 0,
        ]);
    }

    public function update(HrDepartment $department, array $data): HrDepartment
    {
        $parentId = $this->normalizeParentId($data['parent_id'] ?? $department->parent_id);

        if ((string) $parentId === (string) $department->id) {
            throw new BusinessException(
                $this->code(HrDepartmentError::PARENT_INVALID),
                '上级部门不能是自身'
            );
        }

        $parent = $this->assertParentExists($parentId);
        $this->assertNotDescendant($department, $parentId);

        $deptName = $data['dept_name'] ?? $department->dept_name;
        $deptCode = $data['dept_code'] ?? $department->dept_code;
        $this->assertNameUnique($deptName, $parentId, (string) $department->id);
        $this->assertCodeUnique($deptCode, (string) $department->id);

        $leaderUserId = array_key_exists('leader_user_id', $data)
            ? $this->normalizeId($data['leader_user_id'])
            : $department->leader_user_id;
        $this->assertLeaderUser($leaderUserId);

        $level = $parent ? ((int) $parent->dept_level + 1) : 1;
        $ancestors = $parent
            ? trim($parent->ancestors.','.$parent->id, ',')
            : '0';

        $oldParentId = (string) $department->parent_id;

        $department->fill([
            'parent_id' => $parentId,
            'dept_name' => $deptName,
            'dept_code' => $deptCode,
            'ancestors' => $ancestors,
            'dept_level' => $level,
            'leader_user_id' => $leaderUserId,
            'dept_phone' => (string) ($data['dept_phone'] ?? $department->dept_phone),
            'dept_sort' => (int) ($data['dept_sort'] ?? $department->dept_sort),
            'dept_status' => isset($data['dept_status'])
                ? HrDeptStatus::from((int) $data['dept_status'])
                : $department->dept_status,
        ]);
        $department->save();

        if ($oldParentId !== (string) $parentId) {
            $this->refreshDescendants($department);
        }

        return $department->fresh();
    }

    public function updateSort(HrDepartment $department, int $sort): HrDepartment
    {
        $department->dept_sort = $sort;
        $department->save();

        return $department;
    }

    public function updateStatus(HrDepartment $department, int $status): HrDepartment
    {
        $department->dept_status = HrDeptStatus::from($status);
        $department->save();

        return $department;
    }

    public function delete(HrDepartment $department): void
    {
        if ($department->children()->exists()) {
            throw new BusinessException(
                $this->code(HrDepartmentError::DELETE_BLOCKED_HAS_CHILDREN),
                '存在子部门，不可删除'
            );
        }

        if (HrUserDeptPost::query()->where('dept_id', $department->id)->exists()) {
            throw new BusinessException(
                $this->code(HrDepartmentError::DELETE_BLOCKED_HAS_USERS),
                '部门下存在任职人员，不可删除'
            );
        }

        DB::transaction(function () use ($department) {
            HrDeptLeader::query()->where('dept_id', $department->id)->delete();
            $department->delete();
        });
    }

    public function getLeaders(HrDepartment $department): array
    {
        $leaders = HrDeptLeader::query()
            ->where('dept_id', $department->id)
            ->with('user:id,user_name,nick_name')
            ->orderBy('role_type')
            ->orderBy('id')
            ->get();

        return $leaders->map(fn (HrDeptLeader $item) => $this->leaderToArray($item))->all();
    }

    /**
     * @param  array<int, array{user_id: mixed, role_type?: int}>  $leaders
     */
    public function syncLeaders(HrDepartment $department, array $leaders): array
    {
        $normalized = [];
        $userIds = [];

        foreach ($leaders as $row) {
            $userId = $this->normalizeId($row['user_id'] ?? 0);
            if ((string) $userId === '0') {
                continue;
            }
            $this->assertLeaderUser($userId);
            $roleType = (int) ($row['role_type'] ?? HrLeaderRoleType::Primary->value);
            if (! in_array($roleType, array_column(HrLeaderRoleType::cases(), 'value'), true)) {
                $roleType = HrLeaderRoleType::Primary->value;
            }
            if (isset($userIds[(string) $userId])) {
                throw new BusinessException(
                    $this->code(HrDepartmentError::LEADER_DUPLICATED),
                    '同一用户不能重复担任负责人'
                );
            }
            $userIds[(string) $userId] = true;
            $normalized[] = [
                'user_id' => $userId,
                'role_type' => $roleType,
            ];
        }

        $operatorId = Auth::guard('backend')->id();

        DB::transaction(function () use ($department, $normalized, $operatorId) {
            HrDeptLeader::query()->where('dept_id', $department->id)->forceDelete();

            $primaryUserId = 0;
            foreach ($normalized as $row) {
                HrDeptLeader::query()->create([
                    'dept_id' => $department->id,
                    'user_id' => $row['user_id'],
                    'role_type' => HrLeaderRoleType::from($row['role_type']),
                    'created_by' => $operatorId,
                    'updated_by' => $operatorId,
                ]);
                if ($row['role_type'] === HrLeaderRoleType::Primary->value && $primaryUserId === 0) {
                    $primaryUserId = $row['user_id'];
                }
            }

            $department->leader_user_id = $primaryUserId;
            $department->save();
        });

        return $this->getLeaders($department->fresh());
    }

    private function code(int $error): int
    {
        return CodePrefix::HR_DEPARTMENT * 1000 + $error;
    }

    private function normalizeParentId(mixed $parentId): int|string
    {
        if ($parentId === null || $parentId === '' || (int) $parentId === 0) {
            return 0;
        }

        return $parentId;
    }

    private function normalizeId(mixed $id): int|string
    {
        if ($id === null || $id === '' || (int) $id === 0) {
            return 0;
        }

        return $id;
    }

    private function assertParentExists(int|string $parentId): ?HrDepartment
    {
        if ((string) $parentId === '0') {
            return null;
        }

        $parent = HrDepartment::query()->where('id', $parentId)->first();
        if (! $parent) {
            throw new BusinessException(
                $this->code(HrDepartmentError::PARENT_NOT_FOUND),
                '上级部门不存在'
            );
        }

        return $parent;
    }

    private function assertLeaderUser(int|string $userId): void
    {
        if ((string) $userId === '0') {
            return;
        }

        if (! UserAccount::query()->where('id', $userId)->exists()) {
            throw new BusinessException(
                $this->code(HrDepartmentError::LEADER_USER_INVALID),
                '负责人用户无效'
            );
        }
    }

    private function assertNameUnique(string $name, int|string $parentId, ?string $excludeId = null): void
    {
        $exists = HrDepartment::query()
            ->where('parent_id', $parentId)
            ->where('dept_name', $name)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                $this->code(HrDepartmentError::NAME_DUPLICATED),
                '同级部门名称已存在'
            );
        }
    }

    private function assertCodeUnique(string $code, ?string $excludeId = null): void
    {
        $exists = HrDepartment::query()
            ->where('dept_code', $code)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                $this->code(HrDepartmentError::CODE_DUPLICATED),
                '部门编码已存在'
            );
        }
    }

    private function assertNotDescendant(HrDepartment $department, int|string $parentId): void
    {
        if ((string) $parentId === '0') {
            return;
        }

        $descendantIds = $this->collectDescendantIds($department);
        if (in_array((string) $parentId, $descendantIds, true)) {
            throw new BusinessException(
                $this->code(HrDepartmentError::PARENT_INVALID),
                '上级部门不能是当前部门的子级'
            );
        }
    }

    private function refreshDescendants(HrDepartment $department): void
    {
        $children = HrDepartment::query()->where('parent_id', $department->id)->get();
        foreach ($children as $child) {
            $child->dept_level = (int) $department->dept_level + 1;
            $child->ancestors = trim($department->ancestors.','.$department->id, ',');
            $child->save();
            $this->refreshDescendants($child);
        }
    }

    private function collectDescendantIds(HrDepartment $department): array
    {
        $ids = [];
        $children = HrDepartment::query()->where('parent_id', $department->id)->get();
        foreach ($children as $child) {
            $ids[] = (string) $child->id;
            $ids = array_merge($ids, $this->collectDescendantIds($child));
        }

        return $ids;
    }

    private function collectAncestorIds(Collection $all, array $matchedIds): array
    {
        $map = $all->keyBy(fn ($item) => (string) $item->id);
        $keep = [];

        foreach ($matchedIds as $id) {
            $current = $map->get((string) $id);
            while ($current) {
                $keep[(string) $current->id] = true;
                if ((string) $current->parent_id === '0') {
                    break;
                }
                $current = $map->get((string) $current->parent_id);
            }
        }

        return array_keys($keep);
    }

    private function buildTree(Collection $items, int|string $parentId = 0): array
    {
        $tree = [];
        $parentKey = (string) $parentId;
        foreach ($items as $item) {
            if ((string) $item->parent_id !== $parentKey) {
                continue;
            }
            $node = $this->toArray($item);
            $node['children'] = $this->buildTree($items, $item->id);
            $tree[] = $node;
        }

        return $tree;
    }

    private function toArray(HrDepartment $department): array
    {
        return [
            'id' => (string) $department->id,
            'parent_id' => (string) $department->parent_id,
            'dept_name' => $department->dept_name,
            'dept_code' => $department->dept_code,
            'ancestors' => $department->ancestors,
            'dept_level' => (int) $department->dept_level,
            'leader_user_id' => (string) $department->leader_user_id,
            'dept_phone' => $department->dept_phone,
            'dept_sort' => (int) $department->dept_sort,
            'dept_status' => $department->dept_status?->value,
            'dept_status_label' => $department->dept_status?->label(),
        ];
    }

    private function leaderToArray(HrDeptLeader $leader): array
    {
        return [
            'id' => (string) $leader->id,
            'dept_id' => (string) $leader->dept_id,
            'user_id' => (string) $leader->user_id,
            'role_type' => $leader->role_type?->value,
            'role_type_label' => $leader->role_type?->label(),
            'user_name' => $leader->user?->user_name,
            'nick_name' => $leader->user?->nick_name,
        ];
    }
}
