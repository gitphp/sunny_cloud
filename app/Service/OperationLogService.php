<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\OperationLogError;
use App\Enums\OperationAction;
use App\Enums\OperationBizType;
use App\Enums\OperatorStatus;
use App\Exceptions\BusinessException;
use App\Models\OperationLog;
use App\Models\UserAccount;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Throwable;

class OperationLogService
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = OperationLog::query()->orderByDesc('id');

        if (! empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('operator_name', 'like', '%'.$kw.'%')
                    ->orWhere('biz_label', 'like', '%'.$kw.'%')
                    ->orWhere('activity_type', 'like', '%'.$kw.'%')
                    ->orWhere('request_url', 'like', '%'.$kw.'%');
            });
        }

        if (! empty($filters['biz_type'])) {
            $query->where('biz_type', $filters['biz_type']);
        }

        if (! empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['operator_status']) && $filters['operator_status'] !== '' && $filters['operator_status'] !== null) {
            $query->where('operator_status', (int) $filters['operator_status']);
        }

        if (! empty($filters['operator_id'])) {
            $query->where('operator_id', (int) $filters['operator_id']);
        }

        if (! empty($filters['biz_id'])) {
            $query->where('biz_id', (int) $filters['biz_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to'].' 23:59:59');
        }

        return $query->paginate($perPage);
    }

    public function findOrFail(int|string $id): OperationLog
    {
        $log = OperationLog::query()->find($id);
        if (! $log) {
            throw new BusinessException($this->code(OperationLogError::NOT_FOUND), '操作日志不存在');
        }

        return $log;
    }

    /**
     * 安全写入操作日志，失败不影响主业务。
     */
    public function write(array $data): void
    {
        try {
            $request = request();
            $user = $this->resolveOperator();

            $action = $data['action'] ?? OperationAction::Update;
            if ($action instanceof OperationAction) {
                $action = $action->value;
            }

            $status = $data['operator_status'] ?? OperatorStatus::Success;
            if ($status instanceof OperatorStatus) {
                $status = $status->value;
            }

            $bizType = $data['biz_type'] ?? '';
            if ($bizType instanceof OperationBizType) {
                $bizType = $bizType->value;
            }

            OperationLog::query()->create([
                'operator_id' => (int) ($data['operator_id'] ?? ($user?->id ?? 0)),
                'operator_name' => (string) ($data['operator_name'] ?? $this->operatorDisplayName($user)),
                'biz_type' => (string) $bizType,
                'activity_type' => (string) ($data['activity_type'] ?? ''),
                'action' => (string) $action,
                'biz_id' => (int) ($data['biz_id'] ?? 0),
                'biz_label' => mb_substr((string) ($data['biz_label'] ?? ''), 0, 128),
                'old_value' => $data['old_value'] ?? null,
                'new_value' => $data['new_value'] ?? null,
                'operator_status' => (int) $status,
                'error_msg' => mb_substr((string) ($data['error_msg'] ?? ''), 0, 2048),
                'client_ip' => (string) ($data['client_ip'] ?? $request?->ip() ?? ''),
                'user_agent' => mb_substr((string) ($data['user_agent'] ?? $request?->userAgent() ?? ''), 0, 255),
                'request_url' => mb_substr((string) ($data['request_url'] ?? $request?->fullUrl() ?? ''), 0, 255),
                'method_fun' => mb_substr((string) ($data['method_fun'] ?? ''), 0, 128),
                'created_at' => now(),
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function logLogin(
        bool $success,
        string $account,
        ?UserAccount $user = null,
        string $errorMsg = '',
        string $methodFun = 'AuthController@login',
        string $guard = 'backend'
    ): void {
        $this->write([
            'operator_id' => $user?->id ?? 0,
            'operator_name' => $user
                ? $this->operatorDisplayName($user)
                : mb_substr($account, 0, 50),
            'biz_type' => OperationBizType::Auth,
            'activity_type' => $success ? 'user_login' : 'user_login_failed',
            'action' => OperationAction::Login,
            'biz_id' => $user?->id ?? 0,
            'biz_label' => $account,
            'new_value' => [
                'account' => $account,
                'guard' => $guard,
            ],
            'operator_status' => $success ? OperatorStatus::Success : OperatorStatus::Failed,
            'error_msg' => $errorMsg,
            'method_fun' => $methodFun,
        ]);
    }

    public function logCrud(
        OperationAction $action,
        OperationBizType|string $bizType,
        string $activityType,
        int|string $bizId = 0,
        string $bizLabel = '',
        ?array $oldValue = null,
        ?array $newValue = null,
        string $methodFun = '',
        bool $success = true,
        string $errorMsg = ''
    ): void {
        $this->write([
            'biz_type' => $bizType,
            'activity_type' => $activityType,
            'action' => $action,
            'biz_id' => $bizId,
            'biz_label' => $bizLabel,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'operator_status' => $success ? OperatorStatus::Success : OperatorStatus::Failed,
            'error_msg' => $errorMsg,
            'method_fun' => $methodFun,
        ]);
    }

    public function toArray(OperationLog $log): array
    {
        return [
            'id' => (string) $log->id,
            'operator_id' => (string) $log->operator_id,
            'operator_name' => $log->operator_name,
            'biz_type' => $log->biz_type,
            'biz_type_label' => OperationBizType::tryFrom($log->biz_type)?->label() ?? $log->biz_type,
            'activity_type' => $log->activity_type,
            'action' => $log->action?->value ?? $log->action,
            'action_label' => $log->action instanceof OperationAction
                ? $log->action->label()
                : (string) $log->action,
            'biz_id' => (string) $log->biz_id,
            'biz_label' => $log->biz_label,
            'old_value' => $log->old_value,
            'new_value' => $log->new_value,
            'operator_status' => $log->operator_status?->value,
            'operator_status_label' => $log->operator_status?->label(),
            'error_msg' => $log->error_msg,
            'client_ip' => $log->client_ip,
            'user_agent' => $log->user_agent,
            'request_url' => $log->request_url,
            'method_fun' => $log->method_fun,
            'created_at' => optional($log->created_at)?->format('Y-m-d H:i:s'),
        ];
    }

    private function resolveOperator(): ?UserAccount
    {
        /** @var UserAccount|null $user */
        $user = Auth::guard('backend')->user() ?? Auth::guard('frontend')->user();

        return $user instanceof UserAccount ? $user : null;
    }

    private function operatorDisplayName(?UserAccount $user): string
    {
        if (! $user) {
            return '';
        }

        $name = $user->nick_name !== '' ? $user->nick_name : $user->user_name;

        return mb_substr((string) $name, 0, 50);
    }

    private function code(int $error): int
    {
        return CodePrefix::OPERATION_LOG * 1000 + $error;
    }
}
