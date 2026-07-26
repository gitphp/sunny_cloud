<?php

namespace App\Service;

use App\Constants\Code\BossJobError;
use App\Constants\Code\CodePrefix;
use App\Enums\BossJobHot;
use App\Enums\BossJobStatus;
use App\Enums\OperationAction;
use App\Enums\OperationBizType;
use App\Exceptions\BusinessException;
use App\Models\BossJob;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BossJobService
{
    public function __construct(
        private readonly OperationLogService $operationLogService
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = BossJob::query()
            ->orderByDesc('is_hot')
            ->orderByDesc('job_sort')
            ->orderByDesc('id');

        if (! empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('job_title', 'like', '%'.$kw.'%')
                    ->orWhere('department', 'like', '%'.$kw.'%')
                    ->orWhere('workplace', 'like', '%'.$kw.'%')
                    ->orWhere('salary_range', 'like', '%'.$kw.'%');
            });
        }

        if (isset($filters['job_status']) && $filters['job_status'] !== '' && $filters['job_status'] !== null) {
            $query->where('job_status', (int) $filters['job_status']);
        }

        if (isset($filters['is_hot']) && $filters['is_hot'] !== '' && $filters['is_hot'] !== null) {
            $query->where('is_hot', (int) $filters['is_hot']);
        }

        return $query->paginate($perPage);
    }

    public function findOrFail(int|string $id): BossJob
    {
        $job = BossJob::query()->find($id);
        if (! $job) {
            throw new BusinessException($this->code(BossJobError::NOT_FOUND), '职位不存在');
        }

        return $job;
    }

    public function create(array $data): BossJob
    {
        $job = BossJob::query()->create([
            'job_title' => (string) $data['job_title'],
            'department' => (string) ($data['department'] ?? ''),
            'workplace' => (string) ($data['workplace'] ?? ''),
            'experience' => (string) ($data['experience'] ?? ''),
            'education' => (string) ($data['education'] ?? ''),
            'salary_range' => (string) ($data['salary_range'] ?? ''),
            'description' => (string) ($data['description'] ?? ''),
            'requirements' => (string) ($data['requirements'] ?? ''),
            'benefits' => (string) ($data['benefits'] ?? ''),
            'is_hot' => BossJobHot::from((int) ($data['is_hot'] ?? BossJobHot::No->value)),
            'job_status' => BossJobStatus::from((int) ($data['job_status'] ?? BossJobStatus::Pending->value)),
            'expire_at' => $data['expire_at'] ?? null,
            'view_count' => 0,
            'job_sort' => (int) ($data['job_sort'] ?? 0),
        ]);

        $this->operationLogService->logCrud(
            OperationAction::Insert,
            OperationBizType::BossJob,
            'boss_job_created',
            $job->id,
            $job->job_title,
            null,
            $this->toArray($job),
            'BossJobService@create'
        );

        return $job;
    }

    public function update(BossJob $job, array $data): BossJob
    {
        $old = $this->toArray($job);

        $job->fill([
            'job_title' => (string) ($data['job_title'] ?? $job->job_title),
            'department' => (string) ($data['department'] ?? $job->department),
            'workplace' => (string) ($data['workplace'] ?? $job->workplace),
            'experience' => (string) ($data['experience'] ?? $job->experience),
            'education' => (string) ($data['education'] ?? $job->education),
            'salary_range' => (string) ($data['salary_range'] ?? $job->salary_range),
            'description' => (string) ($data['description'] ?? $job->description),
            'requirements' => (string) ($data['requirements'] ?? $job->requirements),
            'benefits' => (string) ($data['benefits'] ?? $job->benefits),
            'is_hot' => isset($data['is_hot'])
                ? BossJobHot::from((int) $data['is_hot'])
                : $job->is_hot,
            'job_status' => isset($data['job_status'])
                ? BossJobStatus::from((int) $data['job_status'])
                : $job->job_status,
            'expire_at' => array_key_exists('expire_at', $data) ? $data['expire_at'] : $job->expire_at,
            'job_sort' => (int) ($data['job_sort'] ?? $job->job_sort),
        ]);
        $job->save();
        $job = $job->fresh();

        $this->operationLogService->logCrud(
            OperationAction::Update,
            OperationBizType::BossJob,
            'boss_job_updated',
            $job->id,
            $job->job_title,
            $old,
            $this->toArray($job),
            'BossJobService@update'
        );

        return $job;
    }

    public function updateSort(BossJob $job, int $sort): BossJob
    {
        $job->job_sort = $sort;
        $job->save();

        return $job;
    }

    public function updateStatus(BossJob $job, int $status): BossJob
    {
        $job->job_status = BossJobStatus::from($status);
        $job->save();

        return $job;
    }

    public function updateHot(BossJob $job, int $isHot): BossJob
    {
        $job->is_hot = BossJobHot::from($isHot);
        $job->save();

        return $job;
    }

    public function delete(BossJob $job): void
    {
        $old = $this->toArray($job);
        $job->delete();

        $this->operationLogService->logCrud(
            OperationAction::Delete,
            OperationBizType::BossJob,
            'boss_job_deleted',
            $old['id'],
            $old['job_title'],
            $old,
            null,
            'BossJobService@delete'
        );
    }

    public function toArray(BossJob $job): array
    {
        return [
            'id' => (string) $job->id,
            'job_title' => $job->job_title,
            'department' => $job->department,
            'workplace' => $job->workplace,
            'experience' => $job->experience,
            'education' => $job->education,
            'salary_range' => $job->salary_range,
            'description' => $job->description,
            'requirements' => $job->requirements,
            'benefits' => $job->benefits,
            'is_hot' => $job->is_hot?->value,
            'is_hot_label' => $job->is_hot?->label(),
            'job_status' => $job->job_status?->value,
            'job_status_label' => $job->job_status?->label(),
            'expire_at' => optional($job->expire_at)?->format('Y-m-d H:i:s'),
            'view_count' => (int) $job->view_count,
            'job_sort' => (int) $job->job_sort,
            'created_at' => optional($job->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($job->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }

    public function toListArray(BossJob $job): array
    {
        $data = $this->toArray($job);
        unset($data['description'], $data['requirements'], $data['benefits']);

        return $data;
    }

    private function code(int $error): int
    {
        return CodePrefix::BOSS_JOB * 1000 + $error;
    }
}
