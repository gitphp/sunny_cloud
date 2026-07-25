<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hr_department')) {
            return;
        }

        Schema::create('hr_department', function (Blueprint $table) {
            $table->id()->comment('部门主键ID');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父部门ID，0=根节点');
            $table->string('dept_name', 64)->default('')->comment('部门名称');
            $table->string('dept_code', 64)->default('')->comment('部门唯一编码');
            $table->string('ancestors', 512)->default('')->comment('祖先ID路径，逗号分隔');
            $table->unsignedTinyInteger('dept_level')->default(1)->comment('层级深度');
            $table->unsignedBigInteger('leader_user_id')->default(0)->comment('部门负责人ID');
            $table->string('dept_phone', 16)->default('')->comment('部门联系电话');
            $table->integer('dept_sort')->default(0)->comment('树形展示排序号');
            $table->tinyInteger('dept_status')->default(1)->comment('状态 0禁用 1正常启用');
            $table->unsignedBigInteger('created_by')->default(0)->comment('创建人用户ID');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');

            $table->unique('dept_code', 'uk_dept_code');
            $table->index('parent_id', 'idx_parent_id');
        });

        DB::statement('ALTER TABLE `hr_department` AUTO_INCREMENT = 920733862005400006');
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_department');
    }
};
