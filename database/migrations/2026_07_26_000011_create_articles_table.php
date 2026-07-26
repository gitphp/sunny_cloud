<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('articles')) {
            return;
        }

        Schema::create('articles', function (Blueprint $table) {
            $table->id()->comment('主键ID（雪花ID或自增）');
            $table->string('title', 255)->default('')->comment('文章标题');
            $table->string('subtitle', 128)->default('')->comment('副标题/摘要');
            $table->string('art_cover', 500)->default('')->comment('封面图URL（支持多图用JSON）');
            $table->longText('art_content')->nullable()->comment('文章正文内容（富文本/Markdown）');
            $table->unsignedTinyInteger('content_type')->default(1)->comment('内容类型：1=富文本，2=Markdown，3=纯文本');
            $table->string('summary', 512)->default('')->comment('文章摘要（自动截取或手动填写）');
            $table->unsignedBigInteger('category_id')->default(0)->comment('分类ID');
            $table->json('tag_ids')->nullable()->comment('标签ID列表');
            $table->unsignedBigInteger('author_id')->default(0)->comment('作者用户ID');
            $table->string('author_name', 16)->default('')->comment('作者姓名（冗余）');
            $table->string('source', 64)->default('')->comment('文章来源');
            $table->string('source_url', 512)->default('')->comment('原文链接');
            $table->unsignedTinyInteger('art_status')->default(1)->comment('状态：1草稿2待审核3审核通过4已发布5已下线6审核驳回7回收站');
            $table->unsignedTinyInteger('is_top')->default(0)->comment('是否置顶：0否1是');
            $table->unsignedTinyInteger('is_original')->default(1)->comment('是否原创：0否1是');
            $table->unsignedTinyInteger('is_commentable')->default(1)->comment('是否允许评论：0否1是');
            $table->string('seo_title', 255)->default('')->comment('SEO标题');
            $table->string('seo_keywords', 255)->default('')->comment('SEO关键词');
            $table->string('seo_description', 512)->default('')->comment('SEO描述');
            $table->json('extra_fields')->nullable()->comment('扩展字段');
            $table->unsignedInteger('view_count')->default(0)->comment('浏览量');
            $table->unsignedInteger('like_count')->default(0)->comment('点赞量');
            $table->unsignedInteger('collect_count')->default(0)->comment('收藏量');
            $table->unsignedInteger('share_count')->default(0)->comment('分享量');
            $table->unsignedInteger('comment_count')->default(0)->comment('评论量');
            $table->dateTime('published_at')->nullable()->comment('发布时间');
            $table->dateTime('created_at')->useCurrent()->comment('创建时间');
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('软删除时间');
            $table->unsignedBigInteger('reviewer_id')->default(0)->comment('审核人ID');
            $table->dateTime('reviewed_at')->nullable()->comment('审核时间');
            $table->string('reject_reason', 512)->nullable()->comment('驳回原因');

            $table->index('author_id', 'idx_author_id');
            $table->index('category_id', 'idx_category_id');
            $table->index('art_status', 'idx_status');
            $table->index('is_top', 'idx_is_top');
            $table->index('published_at', 'idx_published_at');
            $table->index('created_at', 'idx_created_at');
            $table->index('deleted_at', 'idx_deleted_at');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `articles` AUTO_INCREMENT = 920733863034423262');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
