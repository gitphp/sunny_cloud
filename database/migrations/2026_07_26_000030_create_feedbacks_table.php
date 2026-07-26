<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('feedbacks')) {
            return;
        }

        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->string('fb_name', 32)->default('')->comment('联系人姓名');
            $table->string('fb_phone', 16)->nullable()->default('')->comment('联系电话');
            $table->string('fb_email', 32)->nullable()->default('')->comment('邮箱');
            $table->string('fb_company', 32)->nullable()->default('')->comment('公司名称');
            $table->string('fb_title', 128)->default('')->comment('留言标题');
            $table->text('fb_content')->comment('留言内容');
            $table->unsignedTinyInteger('fb_status')->default(0)->comment('0=未处理，1=已处理');
            $table->text('reply_content')->nullable()->comment('回复内容');
            $table->dateTime('replied_at')->nullable()->comment('回复时间');
            $table->string('ip', 32)->default('')->comment('IP地址');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('fb_status', 'idx_status');
            $table->index('created_at', 'idx_created_at');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `feedbacks` AUTO_INCREMENT = 920733863054423256');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
