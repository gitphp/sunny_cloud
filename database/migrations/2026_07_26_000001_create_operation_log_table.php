<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('operation_log')) {
            return;
        }

        Schema::create('operation_log', function (Blueprint $table) {
            $table->id()->comment('主键(雪花ID)');
            $table->unsignedBigInteger('operator_id')->default(0)->comment('操作人ID');
            $table->string('operator_name', 50)->default('')->comment('操作人名称');
            $table->string('biz_type', 16)->default('')->comment('业务模块类型 product/category/customer');
            $table->string('activity_type', 32)->default('')->comment('活动类型如product_created');
            $table->string('action', 16)->default('')->comment('操作类型 (INSERT/UPDATE/DELETE/LOGIN)');
            $table->unsignedBigInteger('biz_id')->default(0)->comment('目标实体ID');
            $table->string('biz_label', 128)->default('')->comment('高亮展示文本');
            $table->json('old_value')->nullable()->comment('修改前的数据快照 (JSON格式)');
            $table->json('new_value')->nullable()->comment('修改后的数据快照 (JSON格式)');
            $table->tinyInteger('operator_status')->default(1)->comment('操作状态 (0:失败, 1:成功)');
            $table->string('error_msg', 2048)->default('')->comment('错误信息 (失败时记录)');
            $table->string('client_ip', 32)->default('')->comment('客户端IP');
            $table->string('user_agent', 255)->default('')->comment('用户浏览器/设备信息');
            $table->string('request_url', 255)->default('')->comment('触发日志的API URL');
            $table->string('method_fun', 128)->default('')->comment('触发日志的方法名');
            $table->dateTime('created_at', 6)->nullable()->comment('发生时间');

            $table->index('operator_id', 'merchant_activity_log_operator_id_index');
            $table->index(['biz_type', 'biz_id'], 'merchant_activity_log_biz_index');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `operation_log` AUTO_INCREMENT = 935126090643669063');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('operation_log');
    }
};
