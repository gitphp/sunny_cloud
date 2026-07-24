<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_role', function (Blueprint $table) {
            $table->id()->comment('角色ID');
            $table->string('role_name', 64)->default('')->comment('角色名称 如 超级管理员');
            $table->string('role_code', 64)->default('')->comment('角色唯一标识（代码鉴权使用，如 finance_admin）');
            $table->tinyInteger('role_type')->default(2)->comment('角色类型: 1=系统内置 2=用户自定义');
            $table->unsignedInteger('role_sort')->default(0)->comment('排序号');
            $table->unsignedTinyInteger('data_scope')->default(1)->comment('数据权限范围 1全部数据 2本部门及下级 3本部门 4仅本人数据 5自定义指定部门');
            $table->json('scope_departments')->nullable()->comment('指定部门IDs，JSON格式');
            $table->unsignedTinyInteger('role_status')->default(1)->comment('0禁用 1启用');
            $table->string('role_remark', 512)->default('')->comment('角色备注');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
            $table->dateTime('deleted_at')->nullable();

            $table->unique(['role_code', 'deleted_at'], 'uk_role_code');
            $table->index('role_status', 'idx_status');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `auth_role` AUTO_INCREMENT = 920733860755423257');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_role');
    }
};
