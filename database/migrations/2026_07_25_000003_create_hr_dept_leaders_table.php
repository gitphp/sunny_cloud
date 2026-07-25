<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hr_dept_leaders')) {
            return;
        }

        Schema::create('hr_dept_leaders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dept_id')->default(0)->comment('组织ID');
            $table->unsignedBigInteger('user_id')->default(0)->comment('负责人ID');
            $table->tinyInteger('role_type')->default(1)->comment('负责人类型：1主要负责人，2次要负责人');
            $table->dateTime('created_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable()->comment('更新人');
            $table->dateTime('deleted_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->index('dept_id', 'index_dept_id');
            $table->index('user_id', 'index_user_id');
        });

        DB::statement('ALTER TABLE `hr_dept_leaders` AUTO_INCREMENT = 920733860747034689');
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_dept_leaders');
    }
};
