<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\FriendLinkError;
use App\Enums\FriendLinkStatus;
use App\Enums\OperationAction;
use App\Enums\OperationBizType;
use App\Exceptions\BusinessException;
use App\Models\FriendLink;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FriendLinkService
{
    public function __construct(
        private readonly OperationLogService $operationLogService
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = FriendLink::query()->orderBy('link_sort')->orderByDesc('id');

        if (! empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('link_name', 'like', '%'.$kw.'%')
                    ->orWhere('link_url', 'like', '%'.$kw.'%')
                    ->orWhere('link_desc', 'like', '%'.$kw.'%');
            });
        }

        if (isset($filters['link_status']) && $filters['link_status'] !== '' && $filters['link_status'] !== null) {
            $query->where('link_status', (int) $filters['link_status']);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): FriendLink
    {
        $this->assertUrl((string) ($data['link_url'] ?? ''));

        $link = FriendLink::query()->create([
            'link_name' => (string) $data['link_name'],
            'link_url' => (string) $data['link_url'],
            'link_logo' => (string) ($data['link_logo'] ?? ''),
            'link_desc' => (string) ($data['link_desc'] ?? ''),
            'link_sort' => (int) ($data['link_sort'] ?? 0),
            'link_status' => FriendLinkStatus::from((int) ($data['link_status'] ?? FriendLinkStatus::Enabled->value)),
        ]);

        $this->operationLogService->logCrud(
            OperationAction::Insert,
            OperationBizType::FriendLink,
            'friend_link_created',
            $link->id,
            $link->link_name,
            null,
            $this->toArray($link),
            'FriendLinkService@create'
        );

        return $link;
    }

    public function update(FriendLink $link, array $data): FriendLink
    {
        $url = (string) ($data['link_url'] ?? $link->link_url);
        $this->assertUrl($url);

        $old = $this->toArray($link);

        $link->fill([
            'link_name' => (string) ($data['link_name'] ?? $link->link_name),
            'link_url' => $url,
            'link_logo' => (string) ($data['link_logo'] ?? $link->link_logo),
            'link_desc' => (string) ($data['link_desc'] ?? $link->link_desc),
            'link_sort' => (int) ($data['link_sort'] ?? $link->link_sort),
            'link_status' => isset($data['link_status'])
                ? FriendLinkStatus::from((int) $data['link_status'])
                : $link->link_status,
        ]);
        $link->save();
        $link = $link->fresh();

        $this->operationLogService->logCrud(
            OperationAction::Update,
            OperationBizType::FriendLink,
            'friend_link_updated',
            $link->id,
            $link->link_name,
            $old,
            $this->toArray($link),
            'FriendLinkService@update'
        );

        return $link;
    }

    public function updateSort(FriendLink $link, int $sort): FriendLink
    {
        $link->link_sort = $sort;
        $link->save();

        return $link;
    }

    public function updateStatus(FriendLink $link, int $status): FriendLink
    {
        $link->link_status = FriendLinkStatus::from($status);
        $link->save();

        return $link;
    }

    public function delete(FriendLink $link): void
    {
        $old = $this->toArray($link);
        $link->delete();

        $this->operationLogService->logCrud(
            OperationAction::Delete,
            OperationBizType::FriendLink,
            'friend_link_deleted',
            $old['id'],
            $old['link_name'],
            $old,
            null,
            'FriendLinkService@delete'
        );
    }

    public function toArray(FriendLink $link): array
    {
        return [
            'id' => (string) $link->id,
            'link_name' => $link->link_name,
            'link_url' => $link->link_url,
            'link_logo' => $link->link_logo,
            'link_desc' => $link->link_desc,
            'link_sort' => (int) $link->link_sort,
            'link_status' => $link->link_status?->value,
            'link_status_label' => $link->link_status?->label(),
            'created_at' => optional($link->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($link->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }

    private function assertUrl(string $url): void
    {
        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new BusinessException($this->code(FriendLinkError::URL_INVALID), '请填写有效的网站链接');
        }
    }

    private function code(int $error): int
    {
        return CodePrefix::FRIEND_LINK * 1000 + $error;
    }
}
