<?php

namespace App\Models;

use App\Enums\WfApproveType;
use App\Enums\WfNodeMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WfFlowNode extends Model
{
    public $timestamps = false;

    protected $table = 'wf_flow_node';

    protected $fillable = [
        'flow_def_id',
        'node_name',
        'node_sort',
        'approve_type',
        'approve_target',
        'node_mode',
        'can_reject',
        'can_add_sign',
        'can_transfer',
        'back_node_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'flow_def_id' => 'integer',
            'node_sort' => 'integer',
            'approve_type' => WfApproveType::class,
            'approve_target' => 'array',
            'node_mode' => WfNodeMode::class,
            'can_reject' => 'integer',
            'can_add_sign' => 'integer',
            'can_transfer' => 'integer',
            'back_node_id' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(WfFlowDefinition::class, 'flow_def_id', 'id');
    }
}
