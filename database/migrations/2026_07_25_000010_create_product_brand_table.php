<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_brand')) {
            return;
        }

        Schema::create('product_brand', function (Blueprint $table) {
            $table->id()->comment('主键(雪花ID)');
            $table->string('brand_code', 36)->default('')->comment('系统产生编码BR000001自增');
            $table->string('brand_name', 32)->default('')->comment('品牌名称');
            $table->string('alias', 64)->default('')->comment('英文别名（可选）');
            $table->unsignedTinyInteger('is_system')->default(0)->comment('是否系统预设 1=系统预设 0=自定义');
            $table->unsignedTinyInteger('is_show')->default(1)->comment('状态 0=隐藏 1=显示');
            $table->unsignedInteger('sort_order')->default(0)->comment('排序');
            $table->string('brand_remark', 512)->default('')->comment('备注');
            $table->dateTime('created_at', 6)->nullable()->comment('创建时间');
            $table->unsignedBigInteger('created_by')->nullable()->comment('创建人');
            $table->dateTime('updated_at', 6)->nullable()->comment('更新时间');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('更新人');
            $table->dateTime('deleted_at', 6)->nullable()->comment('删除时间');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('删除人');
        });

        DB::statement('ALTER TABLE `product_brand` AUTO_INCREMENT = 920733862755000000');
    }

    public function down(): void
    {
        Schema::dropIfExists('product_brand');
    }
};
