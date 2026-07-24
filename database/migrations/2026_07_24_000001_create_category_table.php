<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128)->default('')->comment('分类名称');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级分类ID，0为顶级');
            $table->unsignedInteger('sort')->default(0)->comment('排序号');
            $table->unsignedBigInteger('created_by')->default(0)->comment('创建人');
            $table->unsignedBigInteger('updated_by')->default(0)->comment('更新人');
            $table->dateTime('created_at', 6)->nullable();
            $table->dateTime('updated_at', 6)->nullable();
            $table->dateTime('deleted_at', 6)->nullable();

            $table->index(['parent_id', 'sort'], 'idx_parent_sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
