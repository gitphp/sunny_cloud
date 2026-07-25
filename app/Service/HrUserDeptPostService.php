<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\HrUserDeptPostError;
use App\Enums\HrIsMain;
use App\Exceptions\BusinessException;
use App\Models\HrDepartment;
use App\Models\HrPost;
use App\Models\HrUserDeptPost;
use App\Models\UserAccount;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class HrUserDeptPostService
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = HrUserDeptPost::query()
            ->with([
                'user:id,user_name,nick_name',
                'department:id,dept_name,dept_code',
                'post:id,post_name,post_code',
            ])
            ->orderByDesc('id');

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (! empty($filters['dept_id'])) {
            $query->where('dept_id', $filters['dept_id']);
        }
        if (! empty($filters['post_id'])) {
            $query->where('post_id', $filters['post_id']);
        }
        if (isset($filters['is_main']) && $filters['is_main'] !== '' && $filters['is_main'] !== null) {
            $query->where('is_main', (int) $filters['is_main']);
        }
        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('user', function ($uq) use ($keyword) {
                    $uq->where('user_name', 'like', '%'.$keyword.'%')
                        ->orWhere('nick_name', 'like', '%'.$keyword.'%');
                })->orWhereHas('department', function ($dq) use ($keyword) {
                    $dq->where('dept_name', 'like', '%'.$keyword.'%');
                })->orWhereHas('post', function ($pq) use ($keyword) {
                    $pq->where('post_name', 'like', '%'.$keyword.'%');
                });
            });
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): HrUserDeptPost
    {
        $userId = $data['user_id'];
        $deptId = $data['dept_id'];
        $postId = $data['post_id'];

        $this->assertUserExists($userId);
        $this->assertDeptExists($deptId);
        $this->assertPostExists($postId);
        $this->assertUnique($userId, $deptId, $postId);

        $isMain = HrIsMain::from((int) ($data['is_main'] ?? HrIsMain::PartTime->value));
        if ($isMain === HrIsMain::Main) {
            $this->assertNoOtherMain($userId);
        }

        [$startAt, $endAt] = $this->normalizeDates($data['start_at'] ?? null, $data['end_at'] ?? null);

        return HrUserDeptPost::query()->create([
            'user_id' => $userId,
            'dept_id' => $deptId,
            'post_id' => $postId,
            'is_main' => $isMain,
            'remark' => (string) ($data['remark'] ?? ''),
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);
    }

    public function update(HrUserDeptPost $record, array $data): HrUserDeptPost
    {
        $userId = $data['user_id'] ?? $record->user_id;
        $deptId = $data['dept_id'] ?? $record->dept_id;
        $postId = $data['post_id'] ?? $record->post_id;

        $this->assertUserExists($userId);
        $this->assertDeptExists($deptId);
        $this->assertPostExists($postId);
        $this->assertUnique($userId, $deptId, $postId, (string) $record->id);

        $isMain = isset($data['is_main'])
            ? HrIsMain::from((int) $data['is_main'])
            : $record->is_main;

        if ($isMain === HrIsMain::Main) {
            $this->assertNoOtherMain($userId, (string) $record->id);
        }

        [$startAt, $endAt] = $this->normalizeDates(
            $data['start_at'] ?? $record->start_at,
            $data['end_at'] ?? $record->end_at
        );

        $record->fill([
            'user_id' => $userId,
            'dept_id' => $deptId,
            'post_id' => $postId,
            'is_main' => $isMain,
            'remark' => (string) ($data['remark'] ?? $record->remark),
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);
        $record->save();

        return $record->fresh(['user', 'department', 'post']);
    }

    public function delete(HrUserDeptPost $record): void
    {
        $record->delete();
    }

    public function toArray(HrUserDeptPost $record): array
    {
        return [
            'id' => (string) $record->id,
            'user_id' => (string) $record->user_id,
            'dept_id' => (string) $record->dept_id,
            'post_id' => (string) $record->post_id,
            'is_main' => $record->is_main?->value,
            'is_main_label' => $record->is_main?->label(),
            'remark' => $record->remark,
            'start_at' => optional($record->start_at)?->format('Y-m-d H:i:s'),
            'end_at' => optional($record->end_at)?->format('Y-m-d H:i:s'),
            'user_name' => $record->user?->user_name,
            'nick_name' => $record->user?->nick_name,
            'dept_name' => $record->department?->dept_name,
            'post_name' => $record->post?->post_name,
        ];
    }

    private function code(int $error): int
    {
        return CodePrefix::HR_USER_DEPT_POST * 1000 + $error;
    }

    private function assertUserExists(mixed $userId): void
    {
        if (! UserAccount::query()->where('id', $userId)->exists()) {
            throw new BusinessException(
                $this->code(HrUserDeptPostError::USER_NOT_FOUND),
                '用户不存在'
            );
        }
    }

    private function assertDeptExists(mixed $deptId): void
    {
        if (! HrDepartment::query()->where('id', $deptId)->exists()) {
            throw new BusinessException(
                $this->code(HrUserDeptPostError::DEPT_NOT_FOUND),
                '部门不存在'
            );
        }
    }

    private function assertPostExists(mixed $postId): void
    {
        if (! HrPost::query()->where('id', $postId)->exists()) {
            throw new BusinessException(
                $this->code(HrUserDeptPostError::POST_NOT_FOUND),
                '岗位不存在'
            );
        }
    }

    private function assertUnique(mixed $userId, mixed $deptId, mixed $postId, ?string $excludeId = null): void
    {
        $exists = HrUserDeptPost::query()
            ->where('user_id', $userId)
            ->where('dept_id', $deptId)
            ->where('post_id', $postId)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                $this->code(HrUserDeptPostError::DUPLICATED),
                '同一员工在同一部门不能重复挂同一岗位'
            );
        }
    }

    private function assertNoOtherMain(mixed $userId, ?string $excludeId = null): void
    {
        $exists = HrUserDeptPost::query()
            ->where('user_id', $userId)
            ->where('is_main', HrIsMain::Main)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException(
                $this->code(HrUserDeptPostError::MAIN_ALREADY_EXISTS),
                '该员工已有主岗'
            );
        }
    }

    private function normalizeDates(mixed $startAt, mixed $endAt): array
    {
        $start = $startAt ? Carbon::parse($startAt) : null;
        $end = $endAt ? Carbon::parse($endAt) : null;

        if ($start && $end && $end->lt($start)) {
            throw new BusinessException(
                $this->code(HrUserDeptPostError::DATE_RANGE_INVALID),
                '任职结束时间不能早于开始时间'
            );
        }

        return [$start, $end];
    }
}
