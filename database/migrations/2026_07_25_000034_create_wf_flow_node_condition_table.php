<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wf_flow_node_condition')) {
            return;
        }

        Schema::create('wf_flow_node_condition', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flow_def_id')->default(0)->comment('所属流程ID');
            $table->unsignedBigInteger('pre_node_id')->default(0)->comment('上一个节点ID');
            $table->string('condition_field', 64)->default('')->comment('条件字段');
            $table->string('condition_operator', 16)->default('')->comment('运算符');
            $table->string('condition_value', 128)->default('')->comment('阈值数值');
            $table->unsignedBigInteger('jump_node_id')->default(0)->comment('满足条件跳转节点ID');
            $table->dateTime('created_at')->useCurrent();

            $table->index('flow_def_id', 'idx_flow_def_id');
        });

        DB::statement('ALTER TABLE `wf_flow_node_condition` AUTO_INCREMENT = 920733862004251209');
    }

    public function down(): void
    {
        Schema::dropIfExists('wf_flow_node_condition');
    }
};
