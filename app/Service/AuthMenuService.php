<?php

namespace App\Service;

use App\Constants\Code\AuthMenuError;
use App\Constants\Code\CodePrefix;
use App\Enums\MenuStatus;
use App\Exceptions\BusinessException;
use App\Models\AuthMenu;
use Illuminate\Database\Eloquent\Collection;

class AuthMenuService
{
    public function getTree(?string $keyword = null, bool $enabledOnly = false): array
    {
        $baseQuery = AuthMenu::query()->orderByDesc('menu_sort')->orderBy('id');
        if ($enabledOnly) {
            $baseQuery->where('menu_status', MenuStatus::Enabled);
        }

        if ($keyword !== null && $keyword !== '') {
            $matchedIds = (clone $baseQuery)
                ->where(function ($q) use ($keyword) {
                    $q->where('menu_name', 'like', '%'.$keyword.'%')
                        ->orWhere('menu_path', 'like', '%'.$keyword.'%')
                        ->orWhere('permission_code', 'like', '%'.$keyword.'%');
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

    public function create(array $data): AuthMenu
    {
        $parentId = (int) ($data['parent_id'] ?? 0);
        $this->assertParentExists($parentId);
        $this->assertNameUnique($data['menu_name'], $parentId);

        if (! empty($data['menu_path'])) {
            $this->assertPathUnique($data['menu_path']);
        }

        if (! empty($data['permission_code'])) {
            $this->assertPermissionCodeUnique($data['permission_code']);
        }

        return AuthMenu::query()->create([
            'parent_id' => $parentId,
            'menu_name' => $data['menu_name'],
            'menu_icon' => $data['menu_icon'] ?? '',
            'menu_path' => $data['menu_path'] ?? '',
            'component' => $data['component'] ?? '',
            'permission_code' => $data['permission_code'] ?? '',
            'menu_sort' => (int) ($data['menu_sort'] ?? 0),
            'menu_status' => MenuStatus::from((int) ($data['menu_status'] ?? MenuStatus::Enabled->value)),
        ]);
    }

    public function update(AuthMenu $menu, array $data): AuthMenu
    {
        $parentId = (int) ($data['parent_id'] ?? $menu->parent_id);

        if ($parentId === (int) $menu->id) {
            throw new BusinessException(
                $this->code(AuthMenuError::PARENT_INVALID),
                '上级菜单不能是自身'
            );
        }

        $this->assertParentExists($parentId);
        $this->assertNotDescendant($menu, $parentId);

        $menuName = $data['menu_name'] ?? $menu->menu_name;
        $this->assertNameUnique($menuName, $parentId, (int) $menu->id);

        $menuPath = $data['menu_path'] ?? $menu->menu_path;
        if ($menuPath !== '') {
            $this->assertPathUnique($menuPath, (int) $menu->id);
        }

        $permissionCode = $data['permission_code'] ?? $menu->permission_code;
        if ($permissionCode !== '') {
            $this->assertPermissionCodeUnique($permissionCode, (int) $menu->id);
        }

        $menu->fill([
            'parent_id' => $parentId,
            'menu_name' => $menuName,
            'menu_icon' => $data['menu_icon'] ?? $menu->menu_icon,
            'menu_path' => $menuPath,
            'component' => $data['component'] ?? $menu->component,
            'permission_code' => $permissionCode,
            'menu_sort' => (int) ($data['menu_sort'] ?? $menu->menu_sort),
            'menu_status' => isset($data['menu_status'])
                ? MenuStatus::from((int) $data['menu_status'])
                : $menu->menu_status,
        ]);
        $menu->save();

        return $menu->fresh();
    }

    public function updateSort(AuthMenu $menu, int $sort): AuthMenu
    {
        $menu->menu_sort = $sort;
        $menu->save();

        return $menu;
    }

    public function updateStatus(AuthMenu $menu, int $status): AuthMenu
    {
        $menu->menu_status = MenuStatus::from($status);
        $menu->save();

        return $menu;
    }

    public function delete(AuthMenu $menu): void
    {
        if ($menu->children()->exists()) {
            throw new BusinessException(
                $this->code(AuthMenuError::DELETE_BLOCKED_HAS_CHILDREN),
                '存在子菜单，不可删除'
            );
        }

        $menu->delete();
    }

    private function code(int $error): int
    {
        return CodePrefix::AUTH_MENU * 1000 + $error;
    }

    private function assertParentExists(int $parentId): void
    {
        if ($parentId === 0) {
            return;
        }

        if (! AuthMenu::query()->where('id', $parentId)->exists()) {
            throw new BusinessException(
                $this->code(AuthMenuError::PARENT_NOT_FOUND),
                '上级菜单不存在'
            );
        }
    }

    private function assertNameUnique(string $name, int $parentId, ?int $excludeId = null): void
    {
        $exists = AuthMenu::query()
            ->where('parent_id', $parentId)
            ->where('menu_name', $name)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                $this->code(AuthMenuError::NAME_DUPLICATED),
                '同级菜单名称已存在'
            );
        }
    }

    private function assertPathUnique(string $path, ?int $excludeId = null): void
    {
        $exists = AuthMenu::query()
            ->where('menu_path', $path)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                $this->code(AuthMenuError::PATH_DUPLICATED),
                '路由路径已存在'
            );
        }
    }

    private function assertPermissionCodeUnique(string $code, ?int $excludeId = null): void
    {
        $exists = AuthMenu::query()
            ->where('permission_code', $code)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                $this->code(AuthMenuError::PERMISSION_CODE_DUPLICATED),
                '权限标识已存在'
            );
        }
    }

    private function assertNotDescendant(AuthMenu $menu, int $parentId): void
    {
        if ($parentId === 0) {
            return;
        }

        $descendantIds = $this->collectDescendantIds($menu);
        if (in_array($parentId, $descendantIds, true)) {
            throw new BusinessException(
                $this->code(AuthMenuError::PARENT_INVALID),
                '上级菜单不能是当前菜单的子级'
            );
        }
    }

    private function collectDescendantIds(AuthMenu $menu): array
    {
        $ids = [];
        $children = AuthMenu::query()->where('parent_id', $menu->id)->get();
        foreach ($children as $child) {
            $ids[] = (int) $child->id;
            $ids = array_merge($ids, $this->collectDescendantIds($child));
        }

        return $ids;
    }

    private function collectAncestorIds(Collection $all, array $matchedIds): array
    {
        $map = $all->keyBy('id');
        $keep = [];

        foreach ($matchedIds as $id) {
            $current = $map->get($id);
            while ($current) {
                $keep[$current->id] = true;
                if ((int) $current->parent_id === 0) {
                    break;
                }
                $current = $map->get($current->parent_id);
            }
        }

        return array_map('intval', array_keys($keep));
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

    private function toArray(AuthMenu $menu): array
    {
        return [
            'id' => (string) $menu->id,
            'parent_id' => (string) $menu->parent_id,
            'menu_name' => $menu->menu_name,
            'menu_icon' => $menu->menu_icon,
            'menu_path' => $menu->menu_path,
            'component' => $menu->component,
            'permission_code' => $menu->permission_code,
            'menu_sort' => (int) $menu->menu_sort,
            'menu_status' => $menu->menu_status?->value,
            'menu_status_label' => $menu->menu_status?->label(),
        ];
    }
}
