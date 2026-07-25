<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hr_post')) {
            return;
        }

        Schema::create('hr_post', function (Blueprint $table) {
            $table->id()->comment('岗位主键ID');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级岗位ID，0=顶级根岗位');
            $table->string('post_name', 64)->default('')->comment('岗位名称');
            $table->string('post_code', 64)->default('')->comment('岗位唯一编码');
            $table->integer('post_sort')->default(0)->comment('排序号');
            $table->tinyInteger('post_status')->default(1)->comment('状态 0=禁用 1=正常启用');
            $table->string('remark', 512)->default('')->comment('岗位描述备注');
            $table->unsignedBigInteger('created_by')->default(0)->comment('创建人用户ID');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');

            $table->unique('post_code', 'uk_post_code');
            $table->index('parent_id', 'idx_parent_id');
        });

        DB::statement('ALTER TABLE `hr_post` AUTO_INCREMENT = 920733862004423256');
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_post');
    }
};
