<?php

namespace Database\Seeders;

use App\Enums\BookMarkBold;
use App\Enums\BookMarkStatus;
use App\Enums\CategoryLevel;
use App\Enums\CategoryShowType;
use App\Enums\CategoryStatus;
use App\Enums\CategoryType;
use App\Models\BookMark;
use App\Models\Category;
use Illuminate\Database\Seeder;

class PortalSeeder extends Seeder
{
    public function run(): void
    {
        if (Category::query()->where('category_type', CategoryType::Portal)->exists()) {
            return;
        }

        $channels = [
            ['云盘', 100],
            ['文娱', 90],
            ['工具', 80],
            ['生活', 70],
            ['行业', 60],
            ['综合', 50],
            ['文案', 40],
        ];

        $channelIds = [];
        foreach ($channels as [$name, $sort]) {
            $channelIds[$name] = Category::query()->create([
                'category_name' => $name,
                'parent_id' => 0,
                'category_type' => CategoryType::Portal,
                'show_type' => CategoryShowType::All,
                'cat_status' => CategoryStatus::Visible,
                'level' => CategoryLevel::Level1,
                'sort_order' => $sort,
            ])->id;
        }

        $yunpan = $channelIds['云盘'];
        $sections = [
            ['AI专区', 100, [
                ['ChatGPT', 'https://chatgpt.com', 'OpenAI 对话助手', '全球领先的 AI 对话平台'],
                ['Claude', 'https://claude.ai', 'Anthropic Claude', '擅长长文与代码的 AI 助手'],
                ['Midjourney', 'https://www.midjourney.com', 'AI 绘画', '高质量文生图创作平台'],
                ['通义千问', 'https://tongyi.aliyun.com', '阿里通义', '阿里云大模型与智能应用'],
            ]],
            ['网盘分类', 90, [
                ['阿里云盘', 'https://www.aliyundrive.com', '阿里云盘', '不限速大容量个人云盘'],
                ['百度网盘', 'https://pan.baidu.com', '百度网盘', '老覆盖最广的网盘服务'],
                ['夸克网盘', 'https://pan.quark.cn', '夸克网盘', '高速下载与智能整理'],
                ['天翼云盘', 'https://cloud.189.cn', '天翼云盘', '电信官方云存储服务'],
            ]],
            ['云盘搜索', 80, [
                ['盘搜', 'https://www.pansou.com', '盘搜', '多网盘资源聚合搜索'],
                ['小白盘', 'https://www.xiaobaipan.com', '小白盘', '网盘资源检索工具'],
                ['学搜搜', 'https://www.xuesousou.com', '学搜搜', '学习资料与网盘搜索'],
                ['大圣盘', 'https://www.dashengpan.com', '大圣盘', '网盘链接分享与检索'],
            ]],
            ['云盘网站', 70, [
                ['飞飞导航', 'https://www.fwfly.com', '飞飞导航', '综合资源导航站点'],
                ['藏宝阁', 'https://example.com/cangbao', '藏宝阁', '精选云盘与工具导航'],
                ['优源导航', 'https://example.com/youyuan', '优源导航', '优质资源一站直达'],
                ['云盘之家', 'https://example.com/yunpan', '云盘之家', '网盘工具与技巧合集'],
            ]],
            ['友情链接', 60, [
                ['搜外友链', 'https://example.com/souwai', '搜外友链', '友链交换平台'],
                ['C.DOI 全能导航', 'https://example.com/cdoi', 'C.DOI 全能导航', '综合导航站点'],
                ['爱达杂货铺', 'https://example.com/adzhp', '爱达杂货铺', '效率工具与资源分享'],
                ['不死鸟', 'https://example.com/iui', '不死鸟', '新媒体与运营资源导航'],
            ]],
        ];

        foreach ($sections as [$name, $sort, $books]) {
            $cat = Category::query()->create([
                'category_name' => $name,
                'parent_id' => $yunpan,
                'category_type' => CategoryType::Portal,
                'show_type' => CategoryShowType::All,
                'cat_status' => CategoryStatus::Visible,
                'level' => CategoryLevel::Level2,
                'sort_order' => $sort,
            ]);

            foreach ($books as $i => [$short, $url, $title, $desc]) {
                BookMark::query()->create([
                    'category_id' => $cat->id,
                    'short_title' => mb_substr($short, 0, 16),
                    'book_title' => $title,
                    'book_url' => $url,
                    'book_favicon' => '',
                    'book_desc' => $desc,
                    'sort_order' => 100 - $i * 10,
                    'status' => BookMarkStatus::Normal,
                    'is_bold' => BookMarkBold::Bold,
                    'created_by' => 0,
                ]);
            }
        }
    }
}
