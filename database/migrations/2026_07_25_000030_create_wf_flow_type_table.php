<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wf_flow_type')) {
            return;
        }

        Schema::create('wf_flow_type', function (Blueprint $table) {
            $table->id()->comment('主键ID');
            $table->string('type_name', 32)->default('')->comment('流程类型名称');
            $table->string('type_code', 32)->default('')->comment('唯一编码');
            $table->string('icon', 255)->default('')->comment('前端图标');
            $table->integer('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 0禁用 1启用');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');

            $table->unique('type_code', 'uk_type_code');
        });

        DB::statement('ALTER TABLE `wf_flow_type` AUTO_INCREMENT = 920733862004256487');
    }

    public function down(): void
    {
        Schema::dropIfExists('wf_flow_type');
    }
};
