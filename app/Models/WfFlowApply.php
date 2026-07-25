<?php

namespace App\Models;

use App\Enums\WfApplyStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WfFlowApply extends Model
{
    protected $table = 'wf_flow_apply';

    protected $fillable = [
        'apply_no',
        'flow_type_id',
        'flow_def_id',
        'title',
        'apply_user_id',
        'dept_id',
        'form_data',
        'current_node_id',
        'current_approve_uid',
        'apply_status',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'flow_type_id' => 'integer',
            'flow_def_id' => 'integer',
            'apply_user_id' => 'integer',
            'dept_id' => 'integer',
            'form_data' => 'array',
            'current_node_id' => 'integer',
            'current_approve_uid' => 'integer',
            'apply_status' => WfApplyStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function flowType(): BelongsTo
    {
        return $this->belongsTo(WfFlowType::class, 'flow_type_id', 'id');
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(WfFlowDefinition::class, 'flow_def_id', 'id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'apply_user_id', 'id');
    }

    public function currentNode(): BelongsTo
    {
        return $this->belongsTo(WfFlowNode::class, 'current_node_id', 'id');
    }

    public function currentApprover(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'current_approve_uid', 'id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(WfFlowApproveRecord::class, 'apply_id', 'id')->orderBy('id');
    }

    public function ccUsers(): HasMany
    {
        return $this->hasMany(WfFlowCcUser::class, 'apply_id', 'id');
    }
}
