<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('book_mark')) {
            return;
        }

        Schema::create('book_mark', function (Blueprint $table) {
            $table->id()->comment('主键ID');
            $table->unsignedBigInteger('category_id')->default(0)->comment('所属分类ID，关联 category 表，0表示未分类');
            $table->string('short_title', 32)->default('')->comment('书签短标题');
            $table->string('book_title', 128)->default('')->comment('书签长标题');
            $table->string('book_url', 2048)->default('')->comment('书签链接地址');
            $table->string('book_favicon', 512)->default('')->comment('网站图标URL');
            $table->string('book_desc', 1024)->default('')->comment('书签描述/备注');
            $table->integer('click_count')->default(0)->comment('点击量 热度统计');
            $table->unsignedInteger('sort_order')->default(0)->comment('排序权重，值越小越靠前');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：0-隐藏，1-正常，2-失效');
            $table->unsignedTinyInteger('is_bold')->default(0)->comment('显示：0-加粗，1-正常');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->unsignedBigInteger('created_by')->default(0)->comment('创建人');
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新时间');

            $table->index(['category_id', 'sort_order'], 'idx_category_sort');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `book_mark` AUTO_INCREMENT = 934041315296100356');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('book_mark');
    }
};
