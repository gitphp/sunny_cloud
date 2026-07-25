<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hr_user_dept_post')) {
            return;
        }

        Schema::create('hr_user_dept_post', function (Blueprint $table) {
            $table->id()->comment('自增主键');
            $table->unsignedBigInteger('user_id')->default(0)->comment('员工ID');
            $table->unsignedBigInteger('dept_id')->default(0)->comment('所属部门ID');
            $table->unsignedBigInteger('post_id')->default(0)->comment('兼任岗位ID');
            $table->tinyInteger('is_main')->default(0)->comment('是否为主岗位 0=兼职 1=本职主岗');
            $table->string('remark', 512)->default('')->comment('兼任说明');
            $table->dateTime('start_at')->nullable()->comment('任职开始日期');
            $table->dateTime('end_at')->nullable()->comment('任职结束日期');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['user_id', 'dept_id', 'post_id'], 'uk_user_dept_post');
            $table->index('user_id', 'idx_user_id');
            $table->index('dept_id', 'idx_dept_id');
            $table->index('post_id', 'idx_post_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_user_dept_post');
    }
};
