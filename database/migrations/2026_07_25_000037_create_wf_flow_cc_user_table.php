<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wf_flow_cc_user')) {
            return;
        }

        Schema::create('wf_flow_cc_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apply_id')->default(0)->comment('申请单ID');
            $table->unsignedBigInteger('cc_uid')->default(0)->comment('被抄送用户ID');
            $table->tinyInteger('is_read')->default(0)->comment('0未读 1已读');
            $table->dateTime('read_time')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->unique(['apply_id', 'cc_uid'], 'uk_apply_cc_uid');
            $table->index('cc_uid', 'idx_cc_uid');
        });

        DB::statement('ALTER TABLE `wf_flow_cc_user` AUTO_INCREMENT = 920733862003203210');
    }

    public function down(): void
    {
        Schema::dropIfExists('wf_flow_cc_user');
    }
};
