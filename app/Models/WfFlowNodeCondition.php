<?php

namespace App\Models;

use App\Enums\WfConditionOperator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WfFlowNodeCondition extends Model
{
    public $timestamps = false;

    protected $table = 'wf_flow_node_condition';

    protected $fillable = [
        'flow_def_id',
        'pre_node_id',
        'condition_field',
        'condition_operator',
        'condition_value',
        'jump_node_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'flow_def_id' => 'integer',
            'pre_node_id' => 'integer',
            'condition_operator' => WfConditionOperator::class,
            'jump_node_id' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(WfFlowDefinition::class, 'flow_def_id', 'id');
    }
}
