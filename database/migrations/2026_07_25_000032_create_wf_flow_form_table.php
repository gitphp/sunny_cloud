<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wf_flow_form')) {
            return;
        }

        Schema::create('wf_flow_form', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flow_def_id')->default(0)->comment('绑定流程定义ID');
            $table->string('field_name', 64)->default('')->comment('字段中文名称');
            $table->string('field_key', 64)->default('')->comment('字段英文标识');
            $table->string('field_type', 32)->default('')->comment('组件类型');
            $table->text('field_options')->nullable()->comment('选项JSON');
            $table->tinyInteger('is_required')->default(1)->comment('是否必填');
            $table->integer('sort')->default(0);
            $table->dateTime('created_at')->useCurrent();

            $table->index('flow_def_id', 'idx_flow_def_id');
        });

        DB::statement('ALTER TABLE `wf_flow_form` AUTO_INCREMENT = 920733862003211452');
    }

    public function down(): void
    {
        Schema::dropIfExists('wf_flow_form');
    }
};
