<?php

namespace App\Models;

use App\Enums\OperationAction;
use App\Enums\OperatorStatus;
use Illuminate\Database\Eloquent\Model;

class OperationLog extends Model
{
    protected $table = 'operation_log';

    public const UPDATED_AT = null;

    protected $fillable = [
        'operator_id',
        'operator_name',
        'biz_type',
        'activity_type',
        'action',
        'biz_id',
        'biz_label',
        'old_value',
        'new_value',
        'operator_status',
        'error_msg',
        'client_ip',
        'user_agent',
        'request_url',
        'method_fun',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'operator_id' => 'integer',
            'biz_id' => 'integer',
            'action' => OperationAction::class,
            'operator_status' => OperatorStatus::class,
            'old_value' => 'array',
            'new_value' => 'array',
            'created_at' => 'datetime',
        ];
    }
}
