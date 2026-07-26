<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteConfig extends Model
{
    protected $table = 'site_configs';

    protected $fillable = [
        'conf_group',
        'conf_key',
        'conf_value',
        'conf_desc',
        'input_type',
        'conf_sort',
    ];

    protected function casts(): array
    {
        return [
            'conf_sort' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
