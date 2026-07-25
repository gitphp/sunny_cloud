<?php

namespace App\Models;

use App\Enums\WfActionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WfFlowApproveRecord extends Model
{
    public $timestamps = false;

    protected $table = 'wf_flow_approve_record';

    protected $fillable = [
        'apply_id',
        'node_id',
        'approve_user_id',
        'action_type',
        'target_user_id',
        'approve_opinion',
        'attach_files',
        'operate_at',
    ];

    protected function casts(): array
    {
        return [
            'apply_id' => 'integer',
            'node_id' => 'integer',
            'approve_user_id' => 'integer',
            'action_type' => WfActionType::class,
            'target_user_id' => 'integer',
            'attach_files' => 'array',
            'operate_at' => 'datetime',
        ];
    }

    public function apply(): BelongsTo
    {
        return $this->belongsTo(WfFlowApply::class, 'apply_id', 'id');
    }

    public function node(): BelongsTo
    {
        return $this->belongsTo(WfFlowNode::class, 'node_id', 'id');
    }

    public function approveUser(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'approve_user_id', 'id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'target_user_id', 'id');
    }
}
