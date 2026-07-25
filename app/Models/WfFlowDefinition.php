<?php

namespace App\Models;

use App\Enums\WfPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WfFlowDefinition extends Model
{
    use SoftDeletes;

    protected $table = 'wf_flow_definition';

    protected $fillable = [
        'flow_type_id',
        'flow_name',
        'version',
        'remark',
        'apply_scope',
        'is_publish',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'flow_type_id' => 'integer',
            'version' => 'integer',
            'apply_scope' => 'array',
            'is_publish' => WfPublishStatus::class,
            'created_by' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function flowType(): BelongsTo
    {
        return $this->belongsTo(WfFlowType::class, 'flow_type_id', 'id');
    }

    public function forms(): HasMany
    {
        return $this->hasMany(WfFlowForm::class, 'flow_def_id', 'id')->orderBy('sort')->orderBy('id');
    }

    public function nodes(): HasMany
    {
        return $this->hasMany(WfFlowNode::class, 'flow_def_id', 'id')->orderBy('node_sort')->orderBy('id');
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(WfFlowNodeCondition::class, 'flow_def_id', 'id')->orderBy('id');
    }
}
