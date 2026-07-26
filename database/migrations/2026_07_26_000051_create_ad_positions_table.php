<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ad_positions')) {
            return;
        }

        Schema::create('ad_positions', function (Blueprint $table) {
            $table->id()->comment('主键ID');
            $table->string('ad_title', 128)->default('')->comment('广告标题');
            $table->string('subtitle', 255)->default('')->comment('广告副标题/描述');
            $table->string('cover_url', 512)->default('')->comment('封面图');
            $table->string('cover_mobile', 512)->default('')->comment('移动端封面');
            $table->string('cover_thumb', 512)->default('')->comment('缩略图');
            $table->string('video_url', 512)->default('')->comment('视频URL');
            $table->unsignedTinyInteger('link_type')->default(1)->comment('跳转类型');
            $table->string('link_url', 512)->default('')->comment('跳转链接');
            $table->json('link_params')->nullable()->comment('跳转参数');
            $table->string('app_id', 128)->default('')->comment('小程序AppId');
            $table->string('app_path', 255)->default('')->comment('小程序路径');
            $table->string('position_code', 64)->default('')->comment('广告位编码');
            $table->unsignedTinyInteger('platform')->default(1)->comment('投放平台');
            $table->unsignedTinyInteger('device_type')->default(1)->comment('设备类型');
            $table->unsignedTinyInteger('target_user_type')->default(0)->comment('用户定向');
            $table->json('target_user_group_ids')->nullable();
            $table->json('target_region')->nullable();
            $table->dateTime('start_time')->comment('开始时间');
            $table->dateTime('end_time')->comment('结束时间');
            $table->unsignedTinyInteger('show_time_type')->default(0)->comment('展示时间类型');
            $table->json('time_slots')->nullable();
            $table->json('weekdays')->nullable();
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->unsignedInteger('display_frequency')->default(1)->comment('展示频率');
            $table->unsignedInteger('daily_impression_limit')->default(0);
            $table->unsignedInteger('daily_click_limit')->default(0);
            $table->decimal('budget', 12, 2)->nullable();
            $table->unsignedTinyInteger('cost_type')->default(1);
            $table->decimal('bid_price', 10, 2)->nullable();
            $table->unsignedTinyInteger('status')->default(1)->comment('状态');
            $table->unsignedTinyInteger('audit_status')->default(0)->comment('审核状态');
            $table->unsignedBigInteger('reviewer_id')->default(0);
            $table->dateTime('reviewed_at')->nullable();
            $table->string('reject_reason', 512)->default('');
            $table->unsignedBigInteger('impression_count')->default(0);
            $table->unsignedBigInteger('click_count')->default(0);
            $table->decimal('click_rate', 6, 4)->default(0);
            $table->json('daily_stats')->nullable();
            $table->unsignedBigInteger('created_by')->default(0);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deleted_at')->nullable();

            $table->index('position_code', 'idx_position_code');
            $table->index('status', 'idx_status');
            $table->index(['start_time', 'end_time'], 'idx_start_end_time');
            $table->index('platform', 'idx_platform');
            $table->index('sort', 'idx_sort');
            $table->index('created_at', 'idx_created_at');
            $table->index('deleted_at', 'idx_deleted_at');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `ad_positions` AUTO_INCREMENT = 920733863055423259');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_positions');
    }
};
