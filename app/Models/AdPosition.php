<?php

namespace App\Models;

use App\Enums\AdAuditStatus;
use App\Enums\AdCostType;
use App\Enums\AdDeviceType;
use App\Enums\AdDisplayFrequency;
use App\Enums\AdLinkType;
use App\Enums\AdPlatform;
use App\Enums\AdShowTimeType;
use App\Enums\AdStatus;
use App\Enums\AdTargetUserType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdPosition extends Model
{
    use SoftDeletes;

    protected $table = 'ad_positions';

    protected $fillable = [
        'ad_title',
        'subtitle',
        'cover_url',
        'cover_mobile',
        'cover_thumb',
        'video_url',
        'link_type',
        'link_url',
        'link_params',
        'app_id',
        'app_path',
        'position_code',
        'platform',
        'device_type',
        'target_user_type',
        'target_user_group_ids',
        'target_region',
        'start_time',
        'end_time',
        'show_time_type',
        'time_slots',
        'weekdays',
        'sort',
        'display_frequency',
        'daily_impression_limit',
        'daily_click_limit',
        'budget',
        'cost_type',
        'bid_price',
        'status',
        'audit_status',
        'reviewer_id',
        'reviewed_at',
        'reject_reason',
        'impression_count',
        'click_count',
        'click_rate',
        'daily_stats',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'link_type' => AdLinkType::class,
            'link_params' => 'array',
            'platform' => AdPlatform::class,
            'device_type' => AdDeviceType::class,
            'target_user_type' => AdTargetUserType::class,
            'target_user_group_ids' => 'array',
            'target_region' => 'array',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'show_time_type' => AdShowTimeType::class,
            'time_slots' => 'array',
            'weekdays' => 'array',
            'sort' => 'integer',
            'display_frequency' => AdDisplayFrequency::class,
            'daily_impression_limit' => 'integer',
            'daily_click_limit' => 'integer',
            'budget' => 'decimal:2',
            'cost_type' => AdCostType::class,
            'bid_price' => 'decimal:2',
            'status' => AdStatus::class,
            'audit_status' => AdAuditStatus::class,
            'reviewer_id' => 'integer',
            'reviewed_at' => 'datetime',
            'impression_count' => 'integer',
            'click_count' => 'integer',
            'click_rate' => 'decimal:4',
            'daily_stats' => 'array',
            'created_by' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(AdSlot::class, 'position_code', 'slot_code');
    }
}
