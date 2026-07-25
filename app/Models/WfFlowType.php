<?php

namespace App\Models;

use App\Enums\WfStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WfFlowType extends Model
{
    use SoftDeletes;

    protected $table = 'wf_flow_type';

    protected $fillable = [
        'type_name',
        'type_code',
        'icon',
        'sort',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'sort' => 'integer',
            'status' => WfStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function definitions(): HasMany
    {
        return $this->hasMany(WfFlowDefinition::class, 'flow_type_id', 'id');
    }
}
