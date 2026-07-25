<?php

namespace App\Models;

use App\Enums\WfCcReadStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WfFlowCcUser extends Model
{
    public $timestamps = false;

    protected $table = 'wf_flow_cc_user';

    protected $fillable = [
        'apply_id',
        'cc_uid',
        'is_read',
        'read_time',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'apply_id' => 'integer',
            'cc_uid' => 'integer',
            'is_read' => WfCcReadStatus::class,
            'read_time' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function apply(): BelongsTo
    {
        return $this->belongsTo(WfFlowApply::class, 'apply_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'cc_uid', 'id');
    }
}
