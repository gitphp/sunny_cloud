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
        // 后台默认管理员（登录：admin / admin123）
        UserAccount::query()->updateOrCreate(
            ['user_name' => 'admin'],
            [
                'nick_name' => '管理员',
                'user_mobile' => '13800000000',
                'user_email' => 'admin@example.com',
                'password_hash' => Hash::make('admin123'),
                'password_salt' => '',
                'user_status' => UserStatus::Normal,
                'lock_reason' => '',
                'lock_expire_time' => null,
                'register_channel' => 'web',
                'real_auth_status' => RealAuthStatus::Verified,
            ]
        );

        // 与 sunny_cloud.sql 演示账号对齐：Laravel 默认 bcrypt 对应明文 password
        $demoPasswordHash = Hash::make('password');
        $demoUsers = [
            ['user_name' => 'admin123', 'user_mobile' => '13800000001', 'user_email' => 'admin@company.com', 'nick_name' => '系统管理员'],
            ['user_name' => 'super_admin', 'user_mobile' => '13800000002', 'user_email' => 'super@company.com', 'nick_name' => '超级管理员'],
            ['user_name' => 'editor_zhang', 'user_mobile' => '13800000003', 'user_email' => 'zhangwei@company.com', 'nick_name' => '张伟'],
            ['user_name' => 'ops_li', 'user_mobile' => '13800000004', 'user_email' => 'liming@company.com', 'nick_name' => '李明'],
            ['user_name' => 'pm_wang', 'user_mobile' => '13800000005', 'user_email' => 'wangfang@company.com', 'nick_name' => '王芳'],
            ['user_name' => 'sales_chen', 'user_mobile' => '13800000006', 'user_email' => 'chenjun@company.com', 'nick_name' => '陈军'],
            ['user_name' => 'finance_lin', 'user_mobile' => '13800000007', 'user_email' => 'linna@company.com', 'nick_name' => '林娜'],
            ['user_name' => 'intern_huang', 'user_mobile' => '13800000008', 'user_email' => 'huangxiao@company.com', 'nick_name' => '黄晓'],
        ];

        foreach ($demoUsers as $demo) {
            UserAccount::query()->updateOrCreate(
                ['user_name' => $demo['user_name']],
                [
                    'nick_name' => $demo['nick_name'],
                    'user_mobile' => $demo['user_mobile'],
                    'user_email' => $demo['user_email'],
                    'password_hash' => $demoPasswordHash,
                    'password_salt' => '',
                    'user_status' => UserStatus::Normal,
                    'lock_reason' => '',
                    'lock_expire_time' => null,
                    'register_channel' => 'web',
                    'real_auth_status' => RealAuthStatus::Verified,
                ]
            );
        }
    }
}
