<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wf_flow_definition')) {
            return;
        }

        Schema::create('wf_flow_definition', function (Blueprint $table) {
            $table->id()->comment('流程定义ID');
            $table->unsignedBigInteger('flow_type_id')->default(0)->comment('关联流程类型ID');
            $table->string('flow_name', 128)->default('')->comment('流程名称');
            $table->integer('version')->default(1)->comment('版本号');
            $table->string('remark', 512)->default('')->comment('备注说明');
            $table->text('apply_scope')->nullable()->comment('可发起人员范围JSON');
            $table->tinyInteger('is_publish')->default(0)->comment('是否发布 0草稿 1已发布');
            $table->unsignedBigInteger('created_by')->default(0)->comment('创建人用户ID');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');

            $table->index('flow_type_id', 'idx_flow_type_id');
        });

        DB::statement('ALTER TABLE `wf_flow_definition` AUTO_INCREMENT = 920733862004256569');
    }

    public function down(): void
    {
        Schema::dropIfExists('wf_flow_definition');
    }
};
