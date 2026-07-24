<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auth_permissions', function (Blueprint $table) {
            $table->id()->comment('主键ID');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级权限ID，用于树形结构');
            $table->string('per_name', 64)->default('')->comment('权限名称，如：用户删除');
            $table->string('per_code', 128)->default('')->comment('权限唯一标识，如：user:delete');
            $table->enum('per_type', ['menu', 'button', 'api'])->default('api')->comment('权限类型：menu=菜单，button=按钮，api=接口');
            $table->string('per_path', 255)->default('')->nullable()->comment('前端路由路径或API路径');
            $table->string('per_method', 16)->default('')->nullable()->comment('HTTP方法，仅 type=api 时有效');
            $table->string('per_icon', 64)->default('')->nullable()->comment('菜单图标，仅 type=menu 时有效');
            $table->unsignedInteger('per_sort')->default(0)->comment('排序权重，值越大越靠前');
            $table->unsignedTinyInteger('per_status')->default(1)->comment('状态：0=禁用，1=启用');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrent()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间（软删除）');

            $table->unique('per_code', 'uk_code');
            $table->index('parent_id', 'idx_parent_id');
            $table->index('per_type', 'idx_type');
            $table->index('per_status', 'idx_status');
            $table->index('deleted_at', 'idx_deleted_at');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `auth_permissions` AUTO_INCREMENT = 920733862755423293');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_permissions');
    }
};
