<?php

namespace App\Models;

use App\Enums\FeedbackStatus;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'fb_name',
        'fb_phone',
        'fb_email',
        'fb_company',
        'fb_title',
        'fb_content',
        'fb_status',
        'reply_content',
        'replied_at',
        'ip',
    ];

    protected function casts(): array
    {
        return [
            'fb_status' => FeedbackStatus::class,
            'replied_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
