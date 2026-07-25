<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_category')) {
            return;
        }

        Schema::create('product_category', function (Blueprint $table) {
            $table->id()->comment('主键(雪花ID)');
            $table->string('category_code', 16)->default('')->comment('系统产生编码FL000001自增');
            $table->string('category_name')->default('')->comment('分类名称');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级分类ID 0是一级分类');
            $table->unsignedTinyInteger('level')->default(1)->comment('级别 1=一级 2=二级 3=三级');
            $table->unsignedInteger('product_count')->default(0)->comment('商品数量 冗余');
            $table->string('unit', 32)->default('')->comment('数量单位');
            $table->unsignedTinyInteger('cat_status')->default(1)->comment('状态 0=隐藏 1=显示');
            $table->unsignedInteger('sort_order')->default(0)->comment('排序');
            $table->string('cat_remark', 512)->default('')->comment('备注');
            $table->dateTime('created_at', 6)->nullable()->comment('创建时间');
            $table->unsignedBigInteger('created_by')->nullable()->comment('创建人');
            $table->dateTime('updated_at', 6)->nullable()->comment('更新时间');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('更新人');
            $table->dateTime('deleted_at', 6)->nullable()->comment('删除时间');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('删除人');

            $table->index('parent_id', 'category_parent_id_index');
        });

        DB::statement('ALTER TABLE `product_category` AUTO_INCREMENT = 920733862755420000');
    }

    public function down(): void
    {
        Schema::dropIfExists('product_category');
    }
};
