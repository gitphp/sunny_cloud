<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wf_flow_apply')) {
            return;
        }

        Schema::create('wf_flow_apply', function (Blueprint $table) {
            $table->id()->comment('申请单ID');
            $table->string('apply_no', 64)->default('')->comment('审批单号');
            $table->unsignedBigInteger('flow_type_id')->default(0)->comment('审批类型ID');
            $table->unsignedBigInteger('flow_def_id')->default(0)->comment('流程模板ID');
            $table->string('title', 256)->default('')->comment('单据标题');
            $table->unsignedBigInteger('apply_user_id')->default(0)->comment('发起人UID');
            $table->unsignedBigInteger('dept_id')->default(0)->comment('发起人部门ID');
            $table->longText('form_data')->comment('表单提交内容JSON');
            $table->unsignedBigInteger('current_node_id')->default(0)->comment('当前待审批节点ID');
            $table->unsignedBigInteger('current_approve_uid')->default(0)->comment('当前待处理审批人');
            $table->tinyInteger('apply_status')->default(0)->comment('单据总状态');
            $table->string('remark', 1024)->default('')->comment('发起人备注');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('apply_no', 'uk_apply_no');
            $table->index('apply_user_id', 'idx_apply_user_id');
            $table->index('flow_def_id', 'idx_flow_def_id');
            $table->index('apply_status', 'idx_apply_status');
        });

        DB::statement('ALTER TABLE `wf_flow_apply` AUTO_INCREMENT = 920733862003212321');
    }

    public function down(): void
    {
        Schema::dropIfExists('wf_flow_apply');
    }
};
