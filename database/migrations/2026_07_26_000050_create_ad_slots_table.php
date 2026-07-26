<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ad_slots')) {
            return;
        }

        Schema::create('ad_slots', function (Blueprint $table) {
            $table->id();
            $table->string('slot_code', 32)->default('')->comment('广告位编码');
            $table->string('slot_name', 128)->default('')->comment('广告位名称');
            $table->string('description', 255)->default('')->comment('广告位描述');
            $table->unsignedInteger('width')->default(0)->comment('宽度');
            $table->unsignedInteger('height')->default(0)->comment('高度');
            $table->unsignedInteger('max_items')->default(1)->comment('最大展示数量');
            $table->unsignedTinyInteger('is_system')->default(0)->comment('是否系统预设');
            $table->unsignedTinyInteger('slot_status')->default(1)->comment('状态：0禁用1启用');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deleted_at')->nullable();

            $table->unique('slot_code', 'uk_code');
            $table->index('slot_status', 'idx_status');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `ad_slots` AUTO_INCREMENT = 920733863755423258');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_slots');
    }
};
