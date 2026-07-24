<?php

namespace Database\Seeders;

use App\Models\AuthMenu;
use App\Models\AuthPermission;
use App\Models\AuthRole;
use App\Models\UserAccount;
use Illuminate\Database\Seeder;

class AuthRelationSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = AuthRole::query()->where('role_code', 'super_admin')->first();
        $adminRole = AuthRole::query()->where('role_code', 'admin')->first();
        $adminUser = UserAccount::query()->where('user_name', 'admin')->first();

        if ($superAdmin) {
            $menuIds = AuthMenu::query()->pluck('id')->all();
            $permissionIds = AuthPermission::query()->pluck('id')->all();

            $now = now();
            $menuPayload = [];
            foreach ($menuIds as $id) {
                $menuPayload[$id] = ['created_at' => $now];
            }
            $permPayload = [];
            foreach ($permissionIds as $id) {
                $permPayload[$id] = ['created_at' => $now];
            }

            $superAdmin->menus()->sync($menuPayload);
            $superAdmin->permissions()->sync($permPayload);

            if ($adminRole) {
                $adminRole->menus()->sync($menuPayload);
                $adminRole->permissions()->sync($permPayload);
            }
        }

        if ($adminUser && $superAdmin) {
            $adminUser->roles()->sync([
                $superAdmin->id => ['created_at' => now()],
            ]);
        }
    }
}
