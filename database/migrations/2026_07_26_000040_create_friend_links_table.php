<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('friend_links')) {
            return;
        }

        Schema::create('friend_links', function (Blueprint $table) {
            $table->id();
            $table->string('link_name', 32)->default('')->comment('网站名称');
            $table->string('link_url', 512)->default('')->comment('网站链接');
            $table->string('link_logo', 512)->default('')->comment('网站Logo');
            $table->string('link_desc', 255)->default('')->comment('网站描述');
            $table->unsignedInteger('link_sort')->default(0)->comment('排序越小越前');
            $table->unsignedTinyInteger('link_status')->default(1)->comment('0=禁用，1=启用');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `friend_links` AUTO_INCREMENT = 920733863055413256');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('friend_links');
    }
};
