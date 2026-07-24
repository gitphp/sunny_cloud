<?php

namespace Database\Seeders;

use App\Enums\RealAuthStatus;
use App\Enums\UserStatus;
use App\Models\UserAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserAccountSeeder extends Seeder
{
    public function run(): void
    {
        UserAccount::query()->updateOrCreate(
            ['user_name' => 'admin'],
            [
                'nick_name' => '管理员',
                'user_mobile' => '13800000000',
                'user_email' => 'admin@example.com',
                'password_hash' => Hash::make('admin123'),
                'password_salt' => '',
                'user_status' => UserStatus::Normal,
                'register_channel' => 'web',
                'real_auth_status' => RealAuthStatus::Verified,
            ]
        );
    }
}
