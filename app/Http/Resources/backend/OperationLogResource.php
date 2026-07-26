<?php

namespace App\Http\Resources\backend;

use App\Enums\OperationAction;
use App\Enums\OperationBizType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OperationLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'operator_id' => (string) $this->operator_id,
            'operator_name' => $this->operator_name,
            'biz_type' => $this->biz_type,
            'biz_type_label' => OperationBizType::tryFrom((string) $this->biz_type)?->label() ?? $this->biz_type,
            'activity_type' => $this->activity_type,
            'action' => $this->action instanceof OperationAction
                ? $this->action->value
                : $this->action,
            'action_label' => $this->action instanceof OperationAction
                ? $this->action->label()
                : (string) $this->action,
            'biz_id' => (string) $this->biz_id,
            'biz_label' => $this->biz_label,
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'operator_status' => $this->operator_status?->value,
            'operator_status_label' => $this->operator_status?->label(),
            'error_msg' => $this->error_msg,
            'client_ip' => $this->client_ip,
            'user_agent' => $this->user_agent,
            'request_url' => $this->request_url,
            'method_fun' => $this->method_fun,
            'created_at' => optional($this->created_at)?->format('Y-m-d H:i:s'),
        ];
    }
}
