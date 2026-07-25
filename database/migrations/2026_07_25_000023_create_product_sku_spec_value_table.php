<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_sku_spec_value')) {
            return;
        }

        Schema::create('product_sku_spec_value', function (Blueprint $table) {
            $table->id()->comment('主键(雪花ID)');
            $table->unsignedBigInteger('sku_id')->default(0)->comment('关联SKU表ID');
            $table->unsignedBigInteger('spec_id')->default(0)->comment('关联规格维度ID');
            $table->unsignedBigInteger('spec_value_id')->default(0)->comment('关联规格值ID');
            $table->dateTime('created_at', 6)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('deleted_at', 6)->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->unique(['sku_id', 'spec_id', 'spec_value_id'], 'sku_spec_value_unique');
            $table->index('sku_id', 'sku_spec_sku_id_index');
            $table->index('spec_value_id', 'sku_spec_value_id_index');
        });

        DB::statement('ALTER TABLE `product_sku_spec_value` AUTO_INCREMENT = 920733863300000000');
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sku_spec_value');
    }
};
