<?php

namespace App\Service;

use App\Enums\RealAuthStatus;
use App\Enums\UserStatus;
use App\Exceptions\BusinessException;
use App\Models\UserAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data, string $ip = '', string $device = '', string $channel = 'web'): UserAccount
    {
        $this->assertUniqueFields($data);

        return DB::transaction(function () use ($data, $ip, $device, $channel) {
            return UserAccount::query()->create([
                'user_name' => $data['user_name'],
                'nick_name' => $data['nick_name'],
                'user_mobile' => $data['user_mobile'],
                'user_email' => $data['user_email'] ?? '',
                'password_hash' => Hash::make($data['password']),
                'password_salt' => '',
                'user_status' => UserStatus::Normal,
                'register_ip' => $ip,
                'register_device' => $device,
                'register_channel' => $channel ?: 'web',
                'real_auth_status' => RealAuthStatus::None,
            ]);
        });
    }

    public function login(
        string $account,
        string $password,
        string $ip = '',
        string $region = '',
        string $guard = 'backend'
    ): UserAccount {
        $user = $this->authenticate($account, $password, $ip, $region);
        Auth::guard($guard)->login($user, false);

        return $user;
    }

    public function authenticate(string $account, string $password, string $ip = '', string $region = ''): UserAccount
    {
        $user = UserAccount::query()
            ->where(function ($query) use ($account) {
                $query->where('user_name', $account)
                    ->orWhere('user_mobile', $account)
                    ->orWhere('user_email', $account);
            })
            ->first();

        if (! $user || ! Hash::check($password, $user->password_hash)) {
            throw new BusinessException(2001001, '账号或密码错误');
        }

        if (! $user->isLoginAllowed()) {
            $reason = $user->lock_reason ?: $user->user_status->label();
            throw new BusinessException(2001002, '账号不可登录：'.$reason);
        }

        if ($user->user_status === UserStatus::Frozen
            && $user->lock_expire_time
            && $user->lock_expire_time->isPast()) {
            $user->user_status = UserStatus::Normal;
            $user->lock_reason = '';
            $user->lock_expire_time = null;
        }

        $user->last_login_ip = $ip;
        $user->last_login_region = $region;
        $user->last_login_at = now();
        $user->save();

        return $user;
    }

    public function logout(string $guard = 'backend'): void
    {
        Auth::guard($guard)->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    public function currentUser(string $guard = 'backend'): ?UserAccount
    {
        /** @var UserAccount|null $user */
        $user = Auth::guard($guard)->user();

        return $user;
    }

    private function assertUniqueFields(array $data, ?int $excludeId = null): void
    {
        $checks = [
            'user_name' => [2001010, '用户名已存在'],
            'user_mobile' => [2001011, '手机号已存在'],
            'user_email' => [2001012, '邮箱已存在'],
        ];

        foreach ($checks as $field => [$code, $message]) {
            $value = $data[$field] ?? null;
            if ($value === null || $value === '') {
                continue;
            }

            $exists = UserAccount::query()
                ->where($field, $value)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists();

            if ($exists) {
                throw new BusinessException($code, $message);
            }
        }
    }
}
