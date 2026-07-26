<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\FeedbackError;
use App\Enums\FeedbackStatus;
use App\Enums\OperationAction;
use App\Enums\OperationBizType;
use App\Exceptions\BusinessException;
use App\Models\Feedback;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FeedbackService
{
    public function __construct(
        private readonly OperationLogService $operationLogService
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Feedback::query()->orderByDesc('id');

        if (! empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('fb_name', 'like', '%'.$kw.'%')
                    ->orWhere('fb_phone', 'like', '%'.$kw.'%')
                    ->orWhere('fb_email', 'like', '%'.$kw.'%')
                    ->orWhere('fb_company', 'like', '%'.$kw.'%')
                    ->orWhere('fb_title', 'like', '%'.$kw.'%')
                    ->orWhere('fb_content', 'like', '%'.$kw.'%');
            });
        }

        if (isset($filters['fb_status']) && $filters['fb_status'] !== '' && $filters['fb_status'] !== null) {
            $query->where('fb_status', (int) $filters['fb_status']);
        }

        return $query->paginate($perPage);
    }

    public function findOrFail(int|string $id): Feedback
    {
        $feedback = Feedback::query()->find($id);
        if (! $feedback) {
            throw new BusinessException($this->code(FeedbackError::NOT_FOUND), '留言不存在');
        }

        return $feedback;
    }

    public function create(array $data, string $ip = ''): Feedback
    {
        $feedback = Feedback::query()->create([
            'fb_name' => (string) $data['fb_name'],
            'fb_phone' => (string) ($data['fb_phone'] ?? ''),
            'fb_email' => (string) ($data['fb_email'] ?? ''),
            'fb_company' => (string) ($data['fb_company'] ?? ''),
            'fb_title' => (string) $data['fb_title'],
            'fb_content' => (string) $data['fb_content'],
            'fb_status' => FeedbackStatus::Pending,
            'reply_content' => null,
            'replied_at' => null,
            'ip' => $ip,
        ]);

        $this->operationLogService->logCrud(
            OperationAction::Insert,
            OperationBizType::Feedback,
            'feedback_created',
            $feedback->id,
            $feedback->fb_title,
            null,
            $this->toArray($feedback),
            'FeedbackService@create'
        );

        return $feedback;
    }

    public function reply(Feedback $feedback, string $replyContent): Feedback
    {
        $replyContent = trim($replyContent);
        if ($replyContent === '') {
            throw new BusinessException($this->code(FeedbackError::REPLY_REQUIRED), '请填写回复内容');
        }

        $old = $this->toArray($feedback);

        $feedback->reply_content = $replyContent;
        $feedback->replied_at = now();
        $feedback->fb_status = FeedbackStatus::Handled;
        $feedback->save();

        $feedback = $feedback->fresh();

        $this->operationLogService->logCrud(
            OperationAction::Update,
            OperationBizType::Feedback,
            'feedback_replied',
            $feedback->id,
            $feedback->fb_title,
            $old,
            $this->toArray($feedback),
            'FeedbackService@reply'
        );

        return $feedback;
    }

    public function updateStatus(Feedback $feedback, int $status): Feedback
    {
        $feedback->fb_status = FeedbackStatus::from($status);
        if ($feedback->fb_status === FeedbackStatus::Pending) {
            // 重新打开时不清空历史回复
        }
        $feedback->save();

        return $feedback->fresh();
    }

    public function delete(Feedback $feedback): void
    {
        $old = $this->toArray($feedback);
        $feedback->delete();

        $this->operationLogService->logCrud(
            OperationAction::Delete,
            OperationBizType::Feedback,
            'feedback_deleted',
            $old['id'],
            $old['fb_title'],
            $old,
            null,
            'FeedbackService@delete'
        );
    }

    public function toArray(Feedback $feedback): array
    {
        return [
            'id' => (string) $feedback->id,
            'fb_name' => $feedback->fb_name,
            'fb_phone' => $feedback->fb_phone,
            'fb_email' => $feedback->fb_email,
            'fb_company' => $feedback->fb_company,
            'fb_title' => $feedback->fb_title,
            'fb_content' => $feedback->fb_content,
            'fb_status' => $feedback->fb_status?->value,
            'fb_status_label' => $feedback->fb_status?->label(),
            'reply_content' => $feedback->reply_content,
            'replied_at' => optional($feedback->replied_at)?->format('Y-m-d H:i:s'),
            'ip' => $feedback->ip,
            'created_at' => optional($feedback->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($feedback->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }

    public function toListArray(Feedback $feedback): array
    {
        $data = $this->toArray($feedback);
        unset($data['fb_content'], $data['reply_content']);

        return $data;
    }

    private function code(int $error): int
    {
        return CodePrefix::FEEDBACK * 1000 + $error;
    }
}
