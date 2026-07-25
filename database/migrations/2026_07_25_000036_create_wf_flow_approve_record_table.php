<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wf_flow_approve_record')) {
            return;
        }

        Schema::create('wf_flow_approve_record', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apply_id')->default(0)->comment('关联申请单ID');
            $table->unsignedBigInteger('node_id')->default(0)->comment('流程节点ID');
            $table->unsignedBigInteger('approve_user_id')->default(0)->comment('操作审批人UID');
            $table->tinyInteger('action_type')->default(0)->comment('操作类型');
            $table->unsignedBigInteger('target_user_id')->default(0)->comment('转审/加签目标人ID');
            $table->string('approve_opinion', 2048)->default('')->comment('审批意见');
            $table->text('attach_files')->nullable()->comment('附件地址JSON数组');
            $table->dateTime('operate_at')->useCurrent()->comment('操作时间');

            $table->index('apply_id', 'idx_apply_id');
            $table->index('approve_user_id', 'idx_approve_user_id');
        });

        DB::statement('ALTER TABLE `wf_flow_approve_record` AUTO_INCREMENT = 920733862003213621');
    }

    public function down(): void
    {
        Schema::dropIfExists('wf_flow_approve_record');
    }
};
