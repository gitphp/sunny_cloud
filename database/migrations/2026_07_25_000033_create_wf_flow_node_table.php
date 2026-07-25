<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wf_flow_node')) {
            return;
        }

        Schema::create('wf_flow_node', function (Blueprint $table) {
            $table->id()->comment('节点ID');
            $table->unsignedBigInteger('flow_def_id')->default(0)->comment('关联流程定义ID');
            $table->string('node_name', 64)->default('')->comment('节点名称');
            $table->integer('node_sort')->default(1)->comment('节点执行顺序');
            $table->tinyInteger('approve_type')->default(2)->comment('审批人员类型');
            $table->text('approve_target')->nullable()->comment('审批目标值JSON');
            $table->tinyInteger('node_mode')->default(1)->comment('节点审批模式');
            $table->tinyInteger('can_reject')->default(1)->comment('是否可驳回');
            $table->tinyInteger('can_add_sign')->default(1)->comment('是否允许加签');
            $table->tinyInteger('can_transfer')->default(1)->comment('是否允许转审');
            $table->unsignedBigInteger('back_node_id')->default(0)->comment('驳回回退节点ID');
            $table->dateTime('created_at')->useCurrent();

            $table->index('flow_def_id', 'idx_flow_def_id');
        });

        DB::statement('ALTER TABLE `wf_flow_node` AUTO_INCREMENT = 920733862004251212');
    }

    public function down(): void
    {
        Schema::dropIfExists('wf_flow_node');
    }
};
