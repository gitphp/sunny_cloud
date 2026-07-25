<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_sku')) {
            return;
        }

        Schema::create('product_sku', function (Blueprint $table) {
            $table->id()->comment('主键(雪花ID)');
            $table->unsignedBigInteger('product_id')->default(0)->comment('商品ID');
            $table->string('sku_code', 16)->default('')->comment('SKU编码');
            $table->decimal('price', 10, 2)->unsigned()->default(0)->comment('销售价');
            $table->decimal('market_price', 10, 2)->unsigned()->default(0)->comment('划线价/市场价');
            $table->decimal('cost_price', 10, 2)->unsigned()->default(0)->comment('成本价');
            $table->unsignedInteger('stock_num')->default(0)->comment('库存数量');
            $table->decimal('weight', 10, 2)->unsigned()->default(0)->comment('重量(KG)');
            $table->decimal('volume', 10, 4)->unsigned()->default(0)->comment('体积(m³)');
            $table->unsignedTinyInteger('sale_status')->default(1)->comment('销售状态 0下架 1上架');
            $table->unsignedInteger('sort_order')->default(0)->comment('排序');
            $table->dateTime('created_at', 6)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('deleted_at', 6)->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->unique('sku_code', 'sku_code_unique');
            $table->index('product_id', 'sku_product_id_index');
        });

        DB::statement('ALTER TABLE `product_sku` AUTO_INCREMENT = 920733863200000000');
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sku');
    }
};
