<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\UserError;
use App\Enums\RealAuthStatus;
use App\Enums\RoleStatus;
use App\Enums\UserStatus;
use App\Exceptions\BusinessException;
use App\Models\AuthRole;
use App\Models\UserAccount;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserAccountService
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = UserAccount::query()->orderByDesc('id');

        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('user_name', 'like', "%{$keyword}%")
                    ->orWhere('nick_name', 'like', "%{$keyword}%")
                    ->orWhere('user_mobile', 'like', "%{$keyword}%")
                    ->orWhere('user_email', 'like', "%{$keyword}%");
            });
        }

        if (isset($filters['user_status']) && $filters['user_status'] !== '') {
            $query->where('user_status', (int) $filters['user_status']);
        }

        if (isset($filters['real_auth_status']) && $filters['real_auth_status'] !== '') {
            $query->where('real_auth_status', (int) $filters['real_auth_status']);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data): UserAccount
    {
        $this->assertUnique($data);

        return UserAccount::query()->create([
            'user_name' => $data['user_name'],
            'nick_name' => $data['nick_name'],
            'user_mobile' => $data['user_mobile'],
            'user_email' => $data['user_email'] ?? '',
            'password_hash' => Hash::make($data['password']),
            'password_salt' => '',
            'user_status' => UserStatus::from((int) ($data['user_status'] ?? UserStatus::Normal->value)),
            'lock_reason' => $data['lock_reason'] ?? '',
            'lock_expire_time' => $data['lock_expire_time'] ?? null,
            'register_ip' => $data['register_ip'] ?? '',
            'register_device' => $data['register_device'] ?? '',
            'register_channel' => $data['register_channel'] ?? 'web',
            'real_auth_status' => RealAuthStatus::from((int) ($data['real_auth_status'] ?? RealAuthStatus::None->value)),
        ]);
    }

    public function update(UserAccount $user, array $data): UserAccount
    {
        $this->assertUnique($data, (int) $user->id);

        $user->fill([
            'user_name' => $data['user_name'] ?? $user->user_name,
            'nick_name' => $data['nick_name'] ?? $user->nick_name,
            'user_mobile' => $data['user_mobile'] ?? $user->user_mobile,
            'user_email' => $data['user_email'] ?? $user->user_email,
            'user_status' => isset($data['user_status'])
                ? UserStatus::from((int) $data['user_status'])
                : $user->user_status,
            'lock_reason' => $data['lock_reason'] ?? $user->lock_reason,
            'lock_expire_time' => array_key_exists('lock_expire_time', $data)
                ? $data['lock_expire_time']
                : $user->lock_expire_time,
            'real_auth_status' => isset($data['real_auth_status'])
                ? RealAuthStatus::from((int) $data['real_auth_status'])
                : $user->real_auth_status,
        ]);

        if (! empty($data['password'])) {
            $user->password_hash = Hash::make($data['password']);
            $user->password_salt = '';
        }

        $user->save();

        return $user->fresh();
    }

    public function updateStatus(UserAccount $user, int $status, string $reason = '', ?string $expireTime = null): UserAccount
    {
        $userStatus = UserStatus::from($status);
        $user->user_status = $userStatus;
        $user->lock_reason = $reason;

        if ($userStatus === UserStatus::Frozen) {
            $user->lock_expire_time = $expireTime;
        } elseif ($userStatus === UserStatus::Normal) {
            $user->lock_reason = '';
            $user->lock_expire_time = null;
        }

        $user->save();

        return $user;
    }

    public function delete(UserAccount $user): void
    {
        $user->roles()->detach();
        $user->delete();
    }

    public function getRoleIds(UserAccount $user): array
    {
        return $user->roles()->pluck('auth_role.id')->map(fn ($id) => (string) $id)->values()->all();
    }

    public function syncRoles(UserAccount $user, array $roleIds): array
    {
        $roleIds = array_values(array_unique(array_filter(array_map('strval', $roleIds), fn ($id) => $id !== '' && $id !== '0')));

        if ($roleIds !== []) {
            $count = AuthRole::query()
                ->whereIn('id', $roleIds)
                ->where('role_status', RoleStatus::Enabled)
                ->count();

            if ($count !== count($roleIds)) {
                throw new BusinessException(
                    CodePrefix::USER * 1000 + UserError::INVALID_ROLE_IDS,
                    '存在无效或已禁用角色'
                );
            }
        }

        $payload = [];
        $now = now();
        foreach ($roleIds as $id) {
            $payload[$id] = ['created_at' => $now];
        }

        $user->roles()->sync($payload);

        return $this->getRoleIds($user);
    }

    private function assertUnique(array $data, ?int $excludeId = null): void
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
