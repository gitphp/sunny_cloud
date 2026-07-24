<?php

namespace App\Service;

use App\Constants\Code\AuthPermissionError;
use App\Constants\Code\CodePrefix;
use App\Enums\PermissionStatus;
use App\Enums\PermissionType;
use App\Exceptions\BusinessException;
use App\Models\AuthPermission;
use Illuminate\Database\Eloquent\Collection;

class AuthPermissionService
{
    public function getTree(?string $keyword = null, ?string $type = null, bool $enabledOnly = false): array
    {
        $baseQuery = AuthPermission::query()->orderByDesc('per_sort')->orderBy('id');

        if ($enabledOnly) {
            $baseQuery->where('per_status', PermissionStatus::Enabled);
        }

        if ($type !== null && $type !== '') {
            $baseQuery->where('per_type', $type);
        }

        if ($keyword !== null && $keyword !== '') {
            $matchedIds = (clone $baseQuery)
                ->where(function ($q) use ($keyword) {
                    $q->where('per_name', 'like', '%'.$keyword.'%')
                        ->orWhere('per_code', 'like', '%'.$keyword.'%')
                        ->orWhere('per_path', 'like', '%'.$keyword.'%');
                })
                ->pluck('id')
                ->all();

            if ($matchedIds === []) {
                return [];
            }

            // 搜索时仍取全量（可按启用过滤）再建树，保证祖先节点完整
            $allQuery = AuthPermission::query()->orderByDesc('per_sort')->orderBy('id');
            if ($enabledOnly) {
                $allQuery->where('per_status', PermissionStatus::Enabled);
            }
            $all = $allQuery->get();
            $keepIds = $this->collectAncestorIds($all, $matchedIds);

            return $this->buildTree($all->whereIn('id', $keepIds)->values());
        }

        return $this->buildTree($baseQuery->get());
    }

    public function create(array $data): AuthPermission
    {
        $parentId = $this->normalizeParentId($data['parent_id'] ?? 0);
        $type = PermissionType::from($data['per_type'] ?? PermissionType::Api->value);

        $this->assertParentExists($parentId);
        $this->assertCodeUnique($data['per_code']);
        $this->assertNameUnique($data['per_name'], $parentId);
        $this->assertTypeFields($type, $data);

        return AuthPermission::query()->create([
            'parent_id' => $parentId,
            'per_name' => $data['per_name'],
            'per_code' => $data['per_code'],
            'per_type' => $type,
            'per_path' => $data['per_path'] ?? '',
            'per_method' => strtoupper((string) ($data['per_method'] ?? '')),
            'per_icon' => $data['per_icon'] ?? '',
            'per_sort' => (int) ($data['per_sort'] ?? 0),
            'per_status' => PermissionStatus::from((int) ($data['per_status'] ?? PermissionStatus::Enabled->value)),
        ]);
    }

    public function update(AuthPermission $permission, array $data): AuthPermission
    {
        $parentId = $this->normalizeParentId($data['parent_id'] ?? $permission->parent_id);

        if ((string) $parentId === (string) $permission->id) {
            throw new BusinessException(
                $this->code(AuthPermissionError::PARENT_INVALID),
                '上级权限不能是自身'
            );
        }

        $this->assertParentExists($parentId);
        $this->assertNotDescendant($permission, $parentId);

        $perName = $data['per_name'] ?? $permission->per_name;
        $perCode = $data['per_code'] ?? $permission->per_code;
        $type = isset($data['per_type'])
            ? PermissionType::from($data['per_type'])
            : $permission->per_type;

        $this->assertCodeUnique($perCode, (int) $permission->id);
        $this->assertNameUnique($perName, $parentId, (int) $permission->id);
        $this->assertTypeFields($type, array_merge([
            'per_method' => $permission->per_method,
            'per_path' => $permission->per_path,
            'per_icon' => $permission->per_icon,
        ], $data));

        $permission->fill([
            'parent_id' => $parentId,
            'per_name' => $perName,
            'per_code' => $perCode,
            'per_type' => $type,
            'per_path' => $data['per_path'] ?? $permission->per_path,
            'per_method' => isset($data['per_method'])
                ? strtoupper((string) $data['per_method'])
                : $permission->per_method,
            'per_icon' => $data['per_icon'] ?? $permission->per_icon,
            'per_sort' => (int) ($data['per_sort'] ?? $permission->per_sort),
            'per_status' => isset($data['per_status'])
                ? PermissionStatus::from((int) $data['per_status'])
                : $permission->per_status,
        ]);
        $permission->save();

        return $permission->fresh();
    }

    public function updateSort(AuthPermission $permission, int $sort): AuthPermission
    {
        $permission->per_sort = $sort;
        $permission->save();

        return $permission;
    }

    public function updateStatus(AuthPermission $permission, int $status): AuthPermission
    {
        $permission->per_status = PermissionStatus::from($status);
        $permission->save();

        return $permission;
    }

    public function delete(AuthPermission $permission): void
    {
        if ($permission->children()->exists()) {
            throw new BusinessException(
                $this->code(AuthPermissionError::DELETE_BLOCKED_HAS_CHILDREN),
                '存在子权限，不可删除'
            );
        }

        $permission->delete();
    }

    private function code(int $error): int
    {
        return CodePrefix::AUTH_PERMISSION * 1000 + $error;
    }

    private function normalizeParentId(mixed $parentId): int|string
    {
        if ($parentId === null || $parentId === '' || $parentId === 0 || $parentId === '0') {
            return 0;
        }

        return $parentId;
    }

    private function assertParentExists(int|string $parentId): void
    {
        if ((string) $parentId === '0') {
            return;
        }

        if (! AuthPermission::query()->where('id', $parentId)->exists()) {
            throw new BusinessException(
                $this->code(AuthPermissionError::PARENT_NOT_FOUND),
                '上级权限不存在'
            );
        }
    }

    private function assertCodeUnique(string $code, ?int $excludeId = null): void
    {
        $exists = AuthPermission::query()
            ->where('per_code', $code)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                $this->code(AuthPermissionError::CODE_DUPLICATED),
                '权限标识已存在'
            );
        }
    }

    private function assertNameUnique(string $name, int|string $parentId, ?int $excludeId = null): void
    {
        $exists = AuthPermission::query()
            ->where('parent_id', $parentId)
            ->where('per_name', $name)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                $this->code(AuthPermissionError::NAME_DUPLICATED),
                '同级权限名称已存在'
            );
        }
    }

    private function assertTypeFields(PermissionType $type, array $data): void
    {
        if ($type === PermissionType::Api) {
            $method = strtoupper((string) ($data['per_method'] ?? ''));
            if ($method === '') {
                throw new BusinessException(
                    $this->code(AuthPermissionError::METHOD_REQUIRED),
                    '接口类型必须填写 HTTP 方法'
                );
            }
        }
    }

    private function assertNotDescendant(AuthPermission $permission, int|string $parentId): void
    {
        if ((string) $parentId === '0') {
            return;
        }

        $descendantIds = $this->collectDescendantIds($permission);
        if (in_array((string) $parentId, $descendantIds, true)) {
            throw new BusinessException(
                $this->code(AuthPermissionError::PARENT_INVALID),
                '上级权限不能是当前权限的子级'
            );
        }
    }

    private function collectDescendantIds(AuthPermission $permission): array
    {
        $ids = [];
        $children = AuthPermission::query()->where('parent_id', $permission->id)->get();
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

    private function toArray(AuthPermission $permission): array
    {
        return [
            'id' => (string) $permission->id,
            'parent_id' => (string) $permission->parent_id,
            'per_name' => $permission->per_name,
            'per_code' => $permission->per_code,
            'per_type' => $permission->per_type?->value,
            'per_type_label' => $permission->per_type?->label(),
            'per_path' => $permission->per_path,
            'per_method' => $permission->per_method,
            'per_icon' => $permission->per_icon,
            'per_sort' => (int) $permission->per_sort,
            'per_status' => $permission->per_status?->value,
            'per_status_label' => $permission->per_status?->label(),
        ];
    }
}
