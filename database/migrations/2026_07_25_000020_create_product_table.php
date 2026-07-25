<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product')) {
            return;
        }

        Schema::create('product', function (Blueprint $table) {
            $table->id()->comment('主键(雪花ID)');
            $table->string('auto_code', 36)->default('')->comment('系统产生编码SP000001自增');
            $table->string('product_name', 64)->default('')->comment('商品名称');
            $table->string('product_model', 128)->default('')->comment('商品型号');
            $table->unsignedBigInteger('category_id')->default(0)->comment('商品分类ID');
            $table->unsignedBigInteger('brand_id')->default(0)->comment('品牌ID');
            $table->string('material_quality', 128)->default('')->comment('材质');
            $table->string('filling', 128)->default('')->comment('填充');
            $table->text('short_desc')->nullable()->comment('商品简短描述');
            $table->string('main_image_url', 512)->default('')->comment('主图URL');
            $table->unsignedTinyInteger('product_status')->default(1)->comment('状态 0=已下架 1=已上架');
            $table->unsignedInteger('sort_order')->default(0)->comment('排序');
            $table->dateTime('created_at', 6)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('deleted_at', 6)->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->index('category_id', 'product_category_id_index');
            $table->index('brand_id', 'product_brand_id_index');
            $table->index('auto_code', 'product_auto_code_index');
        });

        DB::statement('ALTER TABLE `product` AUTO_INCREMENT = 920733863000000000');
    }

    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
