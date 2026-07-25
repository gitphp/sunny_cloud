<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category', function (Blueprint $table) {
            $table->id()->comment('主键(雪花ID)');
            $table->string('category_name')->default('')->comment('分类名称');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级分类ID 0是一级分类');
            $table->unsignedTinyInteger('show_type')->default(0)->comment('可见性类型 0=全部可见 1=指定客户可见 2=指定客户不可见');
            $table->unsignedTinyInteger('cat_status')->default(1)->comment('状态 0=隐藏 1=显示');
            $table->unsignedTinyInteger('level')->default(1)->comment('级别 1一级 2二级 3三级');
            $table->unsignedInteger('sort_order')->default(0)->comment('排序');
            $table->string('description', 512)->default('')->comment('分类描述/SEO说明');
            $table->string('cat_remark', 512)->default('')->comment('备注');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->unsignedBigInteger('created_by')->nullable()->comment('创建人');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('更新人');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('删除人');

            $table->index('parent_id', 'category_parent_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category');
    }
};
