<?php

namespace App\Service;

use App\Constants\Code\AuthRoleError;
use App\Constants\Code\CodePrefix;
use App\Enums\DataScope;
use App\Enums\RoleStatus;
use App\Enums\RoleType;
use App\Exceptions\BusinessException;
use App\Models\AuthRole;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuthRoleService
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = AuthRole::query()->orderByDesc('role_sort')->orderByDesc('id');

        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('role_name', 'like', "%{$keyword}%")
                    ->orWhere('role_code', 'like', "%{$keyword}%")
                    ->orWhere('role_remark', 'like', "%{$keyword}%");
            });
        }

        if (isset($filters['role_status']) && $filters['role_status'] !== '') {
            $query->where('role_status', (int) $filters['role_status']);
        }

        if (isset($filters['role_type']) && $filters['role_type'] !== '') {
            $query->where('role_type', (int) $filters['role_type']);
        }

        if (isset($filters['data_scope']) && $filters['data_scope'] !== '') {
            $query->where('data_scope', (int) $filters['data_scope']);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): AuthRole
    {
        $this->assertCodeUnique($data['role_code']);
        $this->assertNameUnique($data['role_name']);
        $scopeDepartments = $this->normalizeScopeDepartments($data);

        return AuthRole::query()->create([
            'role_name' => $data['role_name'],
            'role_code' => $data['role_code'],
            'role_type' => RoleType::from((int) ($data['role_type'] ?? RoleType::Custom->value)),
            'role_sort' => (int) ($data['role_sort'] ?? 0),
            'data_scope' => DataScope::from((int) ($data['data_scope'] ?? DataScope::All->value)),
            'scope_departments' => $scopeDepartments,
            'role_status' => RoleStatus::from((int) ($data['role_status'] ?? RoleStatus::Enabled->value)),
            'role_remark' => $data['role_remark'] ?? '',
        ]);
    }

    public function update(AuthRole $role, array $data): AuthRole
    {
        if ($role->isSystem()) {
            // 系统角色允许改名称/排序/备注/状态/数据范围，不允许改 code / type
            if (isset($data['role_code']) && $data['role_code'] !== $role->role_code) {
                throw new BusinessException(
                    $this->code(AuthRoleError::SYSTEM_FORBIDDEN),
                    '系统内置角色不可修改标识'
                );
            }
            if (isset($data['role_type']) && (int) $data['role_type'] !== $role->role_type->value) {
                throw new BusinessException(
                    $this->code(AuthRoleError::SYSTEM_FORBIDDEN),
                    '系统内置角色不可修改类型'
                );
            }
        }

        $roleCode = $data['role_code'] ?? $role->role_code;
        $roleName = $data['role_name'] ?? $role->role_name;

        $this->assertCodeUnique($roleCode, (int) $role->id);
        $this->assertNameUnique($roleName, (int) $role->id);
        $scopeDepartments = $this->normalizeScopeDepartments($data, $role);

        $role->fill([
            'role_name' => $roleName,
            'role_code' => $roleCode,
            'role_type' => isset($data['role_type'])
                ? RoleType::from((int) $data['role_type'])
                : $role->role_type,
            'role_sort' => (int) ($data['role_sort'] ?? $role->role_sort),
            'data_scope' => isset($data['data_scope'])
                ? DataScope::from((int) $data['data_scope'])
                : $role->data_scope,
            'scope_departments' => $scopeDepartments,
            'role_status' => isset($data['role_status'])
                ? RoleStatus::from((int) $data['role_status'])
                : $role->role_status,
            'role_remark' => $data['role_remark'] ?? $role->role_remark,
        ]);
        $role->save();

        return $role->fresh();
    }

    public function updateSort(AuthRole $role, int $sort): AuthRole
    {
        $role->role_sort = $sort;
        $role->save();

        return $role;
    }

    public function updateStatus(AuthRole $role, int $status): AuthRole
    {
        $role->role_status = RoleStatus::from($status);
        $role->save();

        return $role;
    }

    public function delete(AuthRole $role): void
    {
        if ($role->isSystem()) {
            throw new BusinessException(
                $this->code(AuthRoleError::SYSTEM_FORBIDDEN),
                '系统内置角色不可删除'
            );
        }

        $role->delete();
    }

    private function code(int $error): int
    {
        return CodePrefix::AUTH_ROLE * 1000 + $error;
    }

    private function assertCodeUnique(string $code, ?int $excludeId = null): void
    {
        $exists = AuthRole::query()
            ->where('role_code', $code)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                $this->code(AuthRoleError::CODE_DUPLICATED),
                '角色标识已存在'
            );
        }
    }

    private function assertNameUnique(string $name, ?int $excludeId = null): void
    {
        $exists = AuthRole::query()
            ->where('role_name', $name)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                $this->code(AuthRoleError::NAME_DUPLICATED),
                '角色名称已存在'
            );
        }
    }

    private function normalizeScopeDepartments(array $data, ?AuthRole $role = null): ?array
    {
        $dataScope = isset($data['data_scope'])
            ? DataScope::from((int) $data['data_scope'])
            : ($role?->data_scope ?? DataScope::All);

        if ($dataScope !== DataScope::CustomDepts) {
            return null;
        }

        $depts = $data['scope_departments'] ?? $role?->scope_departments ?? [];
        if (! is_array($depts)) {
            $depts = [];
        }

        $depts = array_values(array_unique(array_filter(array_map('strval', $depts))));

        if ($depts === []) {
            throw new BusinessException(
                $this->code(AuthRoleError::SCOPE_DEPTS_REQUIRED),
                '自定义数据权限时必须指定部门'
            );
        }

        return $depts;
    }
}
