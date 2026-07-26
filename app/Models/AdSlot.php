<?php

namespace App\Models;

use App\Enums\AdSlotStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdSlot extends Model
{
    use SoftDeletes;

    protected $table = 'ad_slots';

    protected $fillable = [
        'slot_code',
        'slot_name',
        'description',
        'width',
        'height',
        'max_items',
        'is_system',
        'slot_status',
    ];

    protected function casts(): array
    {
        return [
            'width' => 'integer',
            'height' => 'integer',
            'max_items' => 'integer',
            'is_system' => 'integer',
            'slot_status' => AdSlotStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function positions(): HasMany
    {
        return $this->hasMany(AdPosition::class, 'position_code', 'slot_code');
    }
}
