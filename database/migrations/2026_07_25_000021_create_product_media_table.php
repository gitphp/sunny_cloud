<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_media')) {
            return;
        }

        Schema::create('product_media', function (Blueprint $table) {
            $table->id()->comment('主键(雪花ID)');
            $table->unsignedBigInteger('product_id')->default(0)->comment('商品ID');
            $table->unsignedTinyInteger('media_type')->default(0)->comment('类型 1=主图 2=详情图 3=视频 4=资质文件 5=其他附件');
            $table->text('file_url')->nullable()->comment('文件URL');
            $table->string('file_name')->default('')->comment('原始文件名');
            $table->string('file_key', 512)->default('')->comment('存储键/路径');
            $table->string('storage_provider', 32)->default('local')->comment('存储提供方');
            $table->string('extension', 16)->default('')->comment('文件拓展名');
            $table->unsignedBigInteger('file_size')->default(0)->comment('字节大小');
            $table->string('file_type', 32)->default('')->comment('MimeType');
            $table->unsignedInteger('sort_order')->default(0)->comment('排序');
            $table->dateTime('created_at', 6)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('deleted_at', 6)->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->index('product_id', 'media_product_id_index');
        });

        DB::statement('ALTER TABLE `product_media` AUTO_INCREMENT = 920733863100000000');
    }

    public function down(): void
    {
        Schema::dropIfExists('product_media');
    }
};
