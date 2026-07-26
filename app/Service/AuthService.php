<?php

namespace App\Service;

use App\Constants\Code\UserError;
use App\Enums\RealAuthStatus;
use App\Enums\UserStatus;
use App\Exceptions\BusinessException;
use App\Models\UserAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthService
{
    private const LOGIN_MAX_ATTEMPTS = 5;

    private const LOGIN_DECAY_SECONDS = 60;

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
        $account = trim($account);
        $throttleKey = $this->loginThrottleKey($account, $ip);

        if (RateLimiter::tooManyAttempts($throttleKey, self::LOGIN_MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw new BusinessException(
                UserError::AUTH_LOGIN_TOO_MANY,
                "登录尝试过于频繁，请 {$seconds} 秒后重试"
            );
        }

        $user = $this->findByLoginAccount($account);

        if (! $user || $user->password_hash === '' || ! Hash::check($password, $user->password_hash)) {
            RateLimiter::hit($throttleKey, self::LOGIN_DECAY_SECONDS);
            throw new BusinessException(UserError::AUTH_ACCOUNT_OR_PASSWORD, '账号或密码错误');
        }

        $this->ensureLoginAllowed($user);
        $this->releaseExpiredFreeze($user);

        $user->last_login_ip = $ip;
        $user->last_login_region = $region;
        $user->last_login_at = now();
        $user->save();

        RateLimiter::clear($throttleKey);

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

    /**
     * 按 user_account 表字段查找：用户名 → 手机号 → 邮箱（跳过空值，避免唯一空串误匹配）
     */
    private function findByLoginAccount(string $account): ?UserAccount
    {
        if ($account === '') {
            return null;
        }

        $byName = UserAccount::query()->where('user_name', $account)->first();
        if ($byName) {
            return $byName;
        }

        if (preg_match('/^1[3-9]\d{9}$/', $account) === 1) {
            return UserAccount::query()
                ->where('user_mobile', $account)
                ->where('user_mobile', '!=', '')
                ->first();
        }

        if (str_contains($account, '@')) {
            return UserAccount::query()
                ->where('user_email', $account)
                ->where('user_email', '!=', '')
                ->first();
        }

        return UserAccount::query()
            ->where(function ($query) use ($account) {
                $query->where(function ($q) use ($account) {
                    $q->where('user_mobile', $account)->where('user_mobile', '!=', '');
                })->orWhere(function ($q) use ($account) {
                    $q->where('user_email', $account)->where('user_email', '!=', '');
                });
            })
            ->first();
    }

    private function ensureLoginAllowed(UserAccount $user): void
    {
        if ($user->isLoginAllowed()) {
            return;
        }

        $message = match ($user->user_status) {
            UserStatus::Disabled => '账号已禁用',
            UserStatus::Frozen => $user->lock_reason !== ''
                ? '账号已冻结：'.$user->lock_reason
                : '账号已冻结',
            UserStatus::Cancelled => '账号已注销',
            default => '账号不可登录',
        };

        throw new BusinessException(UserError::AUTH_ACCOUNT_DISABLED, $message);
    }

    private function releaseExpiredFreeze(UserAccount $user): void
    {
        if ($user->user_status !== UserStatus::Frozen) {
            return;
        }

        if (! $user->lock_expire_time || ! $user->lock_expire_time->isPast()) {
            return;
        }

        $user->user_status = UserStatus::Normal;
        $user->lock_reason = '';
        $user->lock_expire_time = null;
    }

    private function loginThrottleKey(string $account, string $ip): string
    {
        return 'login:'.sha1(mb_strtolower($account).'|'.$ip);
    }

    private function assertUniqueFields(array $data, ?int $excludeId = null): void
    {
        $checks = [
            'user_name' => [UserError::AUTH_USERNAME_EXISTS, '用户名已存在'],
            'user_mobile' => [UserError::AUTH_MOBILE_EXISTS, '手机号已存在'],
            'user_email' => [UserError::AUTH_EMAIL_EXISTS, '邮箱已存在'],
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
