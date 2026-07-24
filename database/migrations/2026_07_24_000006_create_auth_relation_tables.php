<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_role_menus', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->default(0)->comment('角色ID（关联 auth_role.id）');
            $table->unsignedBigInteger('menu_id')->default(0)->comment('菜单ID（关联 auth_menus.id）');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');

            $table->primary(['role_id', 'menu_id']);
        });

        Schema::create('auth_role_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->default(0)->comment('角色ID（关联 auth_role.id）');
            $table->unsignedBigInteger('permission_id')->default(0)->comment('权限ID（关联 auth_permissions.id）');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');

            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('auth_user_role', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->default(0)->comment('用户ID');
            $table->unsignedBigInteger('role_id')->default(0)->comment('角色ID');
            $table->dateTime('created_at')->nullable()->useCurrent()->comment('创建时间');

            $table->primary(['user_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_user_role');
        Schema::dropIfExists('auth_role_permissions');
        Schema::dropIfExists('auth_role_menus');
    }
};
