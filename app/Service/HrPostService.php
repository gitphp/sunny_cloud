<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\HrPostError;
use App\Enums\HrPostStatus;
use App\Exceptions\BusinessException;
use App\Models\HrPost;
use App\Models\HrUserDeptPost;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class HrPostService
{
    public function getTree(?string $keyword = null): array
    {
        $baseQuery = HrPost::query()->orderByDesc('post_sort')->orderBy('id');

        if ($keyword !== null && $keyword !== '') {
            $matchedIds = (clone $baseQuery)
                ->where(function ($q) use ($keyword) {
                    $q->where('post_name', 'like', '%'.$keyword.'%')
                        ->orWhere('post_code', 'like', '%'.$keyword.'%');
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

    public function create(array $data): HrPost
    {
        $parentId = $this->normalizeParentId($data['parent_id'] ?? 0);
        $this->assertParentExists($parentId);

        $postName = $data['post_name'];
        $postCode = $data['post_code'];
        $this->assertNameUnique($postName, $parentId);
        $this->assertCodeUnique($postCode);

        return HrPost::query()->create([
            'parent_id' => $parentId,
            'post_name' => $postName,
            'post_code' => $postCode,
            'post_sort' => (int) ($data['post_sort'] ?? 0),
            'post_status' => HrPostStatus::from((int) ($data['post_status'] ?? HrPostStatus::Enabled->value)),
            'remark' => (string) ($data['remark'] ?? ''),
            'created_by' => Auth::guard('backend')->id() ?? 0,
        ]);
    }

    public function update(HrPost $post, array $data): HrPost
    {
        $parentId = $this->normalizeParentId($data['parent_id'] ?? $post->parent_id);

        if ((string) $parentId === (string) $post->id) {
            throw new BusinessException(
                $this->code(HrPostError::PARENT_INVALID),
                '上级岗位不能是自身'
            );
        }

        $this->assertParentExists($parentId);
        $this->assertNotDescendant($post, $parentId);

        $postName = $data['post_name'] ?? $post->post_name;
        $postCode = $data['post_code'] ?? $post->post_code;
        $this->assertNameUnique($postName, $parentId, (string) $post->id);
        $this->assertCodeUnique($postCode, (string) $post->id);

        $post->fill([
            'parent_id' => $parentId,
            'post_name' => $postName,
            'post_code' => $postCode,
            'post_sort' => (int) ($data['post_sort'] ?? $post->post_sort),
            'post_status' => isset($data['post_status'])
                ? HrPostStatus::from((int) $data['post_status'])
                : $post->post_status,
            'remark' => (string) ($data['remark'] ?? $post->remark),
        ]);
        $post->save();

        return $post->fresh();
    }

    public function updateSort(HrPost $post, int $sort): HrPost
    {
        $post->post_sort = $sort;
        $post->save();

        return $post;
    }

    public function updateStatus(HrPost $post, int $status): HrPost
    {
        $post->post_status = HrPostStatus::from($status);
        $post->save();

        return $post;
    }

    public function delete(HrPost $post): void
    {
        if ($post->children()->exists()) {
            throw new BusinessException(
                $this->code(HrPostError::DELETE_BLOCKED_HAS_CHILDREN),
                '存在子岗位，不可删除'
            );
        }

        if (HrUserDeptPost::query()->where('post_id', $post->id)->exists()) {
            throw new BusinessException(
                $this->code(HrPostError::DELETE_BLOCKED_HAS_USERS),
                '岗位下存在任职人员，不可删除'
            );
        }

        $post->delete();
    }

    private function code(int $error): int
    {
        return CodePrefix::HR_POST * 1000 + $error;
    }

    private function normalizeParentId(mixed $parentId): int|string
    {
        if ($parentId === null || $parentId === '' || (int) $parentId === 0) {
            return 0;
        }

        return $parentId;
    }

    private function assertParentExists(int|string $parentId): void
    {
        if ((string) $parentId === '0') {
            return;
        }

        if (! HrPost::query()->where('id', $parentId)->exists()) {
            throw new BusinessException(
                $this->code(HrPostError::PARENT_NOT_FOUND),
                '上级岗位不存在'
            );
        }
    }

    private function assertNameUnique(string $name, int|string $parentId, ?string $excludeId = null): void
    {
        $exists = HrPost::query()
            ->where('parent_id', $parentId)
            ->where('post_name', $name)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                $this->code(HrPostError::NAME_DUPLICATED),
                '同级岗位名称已存在'
            );
        }
    }

    private function assertCodeUnique(string $code, ?string $excludeId = null): void
    {
        $exists = HrPost::query()
            ->where('post_code', $code)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                $this->code(HrPostError::CODE_DUPLICATED),
                '岗位编码已存在'
            );
        }
    }

    private function assertNotDescendant(HrPost $post, int|string $parentId): void
    {
        if ((string) $parentId === '0') {
            return;
        }

        $descendantIds = $this->collectDescendantIds($post);
        if (in_array((string) $parentId, $descendantIds, true)) {
            throw new BusinessException(
                $this->code(HrPostError::PARENT_INVALID),
                '上级岗位不能是当前岗位的子级'
            );
        }
    }

    private function collectDescendantIds(HrPost $post): array
    {
        $ids = [];
        $children = HrPost::query()->where('parent_id', $post->id)->get();
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

    private function toArray(HrPost $post): array
    {
        return [
            'id' => (string) $post->id,
            'parent_id' => (string) $post->parent_id,
            'post_name' => $post->post_name,
            'post_code' => $post->post_code,
            'post_sort' => (int) $post->post_sort,
            'post_status' => $post->post_status?->value,
            'post_status_label' => $post->post_status?->label(),
            'remark' => $post->remark,
        ];
    }
}
