<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('site_configs')) {
            return;
        }

        Schema::create('site_configs', function (Blueprint $table) {
            $table->id();
            $table->string('conf_group', 32)->default('basic')->comment('配置分组：basic, seo, contact, social');
            $table->string('conf_key', 128)->default('')->comment('配置键名');
            $table->text('conf_value')->nullable()->comment('配置值');
            $table->string('conf_desc', 255)->default('')->comment('配置说明');
            $table->string('input_type', 30)->default('text')->comment('输入类型：text, textarea, image, file, json');
            $table->unsignedInteger('conf_sort')->default(0)->comment('排序');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `site_configs` AUTO_INCREMENT = 920733863044423255');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_configs');
    }
};
