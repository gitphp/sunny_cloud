<?php

namespace App\Models;

use App\Enums\FriendLinkStatus;
use Illuminate\Database\Eloquent\Model;

class FriendLink extends Model
{
    protected $table = 'friend_links';

    protected $fillable = [
        'link_name',
        'link_url',
        'link_logo',
        'link_desc',
        'link_sort',
        'link_status',
    ];

    protected function casts(): array
    {
        return [
            'link_sort' => 'integer',
            'link_status' => FriendLinkStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
