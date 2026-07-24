<?php

namespace App\Models;

use App\Enums\RealAuthStatus;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserAccount extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'user_account';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'user_name',
        'nick_name',
        'user_mobile',
        'user_email',
        'password_hash',
        'password_salt',
        'user_status',
        'lock_reason',
        'lock_expire_time',
        'last_login_ip',
        'last_login_region',
        'last_login_at',
        'register_ip',
        'register_device',
        'register_channel',
        'real_auth_status',
    ];

    protected $hidden = [
        'password_hash',
        'password_salt',
    ];

    protected function casts(): array
    {
        return [
            'user_status' => UserStatus::class,
            'real_auth_status' => RealAuthStatus::class,
            'lock_expire_time' => 'datetime',
            'last_login_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Auth 契约：密码字段映射到 password_hash
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function isLoginAllowed(): bool
    {
        if ($this->user_status === UserStatus::Normal) {
            return true;
        }

        if ($this->user_status === UserStatus::Frozen) {
            if ($this->lock_expire_time && $this->lock_expire_time->isPast()) {
                return true;
            }

            return false;
        }

        return false;
    }
}
