<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('boss_job')) {
            return;
        }

        Schema::create('boss_job', function (Blueprint $table) {
            $table->id();
            $table->string('job_title', 64)->default('')->comment('职位名称');
            $table->string('department', 64)->default('')->comment('所属部门');
            $table->string('workplace', 128)->default('')->comment('工作地点');
            $table->string('experience', 64)->nullable()->default('')->comment('经验要求');
            $table->string('education', 64)->nullable()->default('')->comment('学历要求');
            $table->string('salary_range', 64)->nullable()->default('')->comment('薪资范围');
            $table->text('description')->nullable()->comment('职位描述');
            $table->text('requirements')->nullable()->comment('任职要求');
            $table->text('benefits')->nullable()->comment('福利待遇');
            $table->unsignedTinyInteger('is_hot')->default(0)->comment('是否急聘');
            $table->unsignedTinyInteger('job_status')->default(1)->comment('1=待发布，2=发布中，3=已关闭');
            $table->dateTime('expire_at')->nullable()->comment('过期时间');
            $table->unsignedInteger('view_count')->default(0)->comment('浏览量');
            $table->unsignedInteger('job_sort')->default(0);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deleted_at')->nullable();
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `boss_job` AUTO_INCREMENT = 920733863004423259');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('boss_job');
    }
};
