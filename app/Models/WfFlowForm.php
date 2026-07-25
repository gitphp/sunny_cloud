<?php

namespace App\Models;

use App\Enums\WfFieldType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WfFlowForm extends Model
{
    public $timestamps = false;

    protected $table = 'wf_flow_form';

    protected $fillable = [
        'flow_def_id',
        'field_name',
        'field_key',
        'field_type',
        'field_options',
        'is_required',
        'sort',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'flow_def_id' => 'integer',
            'field_type' => WfFieldType::class,
            'field_options' => 'array',
            'is_required' => 'integer',
            'sort' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(WfFlowDefinition::class, 'flow_def_id', 'id');
    }
}
