<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_specification_value')) {
            return;
        }

        Schema::create('product_specification_value', function (Blueprint $table) {
            $table->id()->comment('主键(雪花ID)');
            $table->unsignedBigInteger('spec_id')->default(0)->comment('规格ID');
            $table->string('value_code', 36)->default('')->comment('系统产生编码GV000001自增');
            $table->string('value')->default('')->comment('规格值');
            $table->unsignedInteger('sort_order')->default(0)->comment('排序');
            $table->unsignedTinyInteger('value_status')->default(1)->comment('状态 0=隐藏 1=显示');
            $table->dateTime('created_at', 6)->nullable()->comment('创建时间');
            $table->unsignedBigInteger('created_by')->nullable()->comment('创建人');
            $table->dateTime('updated_at', 6)->nullable()->comment('更新时间');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('更新人');
            $table->dateTime('deleted_at', 6)->nullable()->comment('删除时间');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('删除人');

            $table->index('spec_id', 'spec_value_spec_id_index');
        });

        DB::statement('ALTER TABLE `product_specification_value` AUTO_INCREMENT = 920733862755320000');
    }

    public function down(): void
    {
        Schema::dropIfExists('product_specification_value');
    }
};
