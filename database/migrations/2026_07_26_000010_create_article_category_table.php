<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('article_category')) {
            return;
        }

        Schema::create('article_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级ID，0表示顶级');
            $table->string('cat_name', 32)->default('')->comment('分类名称');
            $table->string('cat_url', 32)->default('')->comment('URL别名，如：company-news');
            $table->string('description', 255)->default('')->comment('分类描述');
            $table->unsignedInteger('cat_sort')->default(0)->comment('排序权重');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：0=禁用，1=启用');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deleted_at')->nullable();

            $table->index('parent_id', 'idx_parent_id');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `article_category` AUTO_INCREMENT = 920733863034423263');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('article_category');
    }
};
