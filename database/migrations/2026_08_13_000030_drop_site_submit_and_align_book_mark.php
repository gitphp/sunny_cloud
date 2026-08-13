<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('site_submit');

        if (! Schema::hasTable('book_mark')) {
            return;
        }

        if (! Schema::hasColumn('book_mark', 'click_count')) {
            Schema::table('book_mark', function (Blueprint $table) {
                $table->integer('click_count')->default(0)->after('book_desc')->comment('点击量 热度统计');
            });
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `book_mark` MODIFY COLUMN `short_title` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '书签短标题'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('book_mark')) {
            return;
        }

        if (Schema::hasColumn('book_mark', 'click_count')) {
            Schema::table('book_mark', function (Blueprint $table) {
                $table->dropColumn('click_count');
            });
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `book_mark` MODIFY COLUMN `short_title` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '书签短标题'");
        }
    }
};
