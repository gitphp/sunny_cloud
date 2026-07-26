<?php

namespace Database\Seeders;

use App\Models\SiteConfig;
use Illuminate\Database\Seeder;

class MingyangSiteConfigSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['conf_group' => 'basic', 'conf_key' => 'site_name', 'conf_value' => '名扬科技', 'conf_desc' => '站点名称', 'conf_sort' => 10],
            ['conf_group' => 'basic', 'conf_key' => 'site_title', 'conf_value' => '深圳市名扬科技 — 企业数字化与智能云服务', 'conf_desc' => '站点标题', 'conf_sort' => 20],
            ['conf_group' => 'basic', 'conf_key' => 'site_keywords', 'conf_value' => '名扬科技,深圳软件,数字化转型,企业云,AI平台', 'conf_desc' => '站点关键词', 'conf_sort' => 30],
            ['conf_group' => 'basic', 'conf_key' => 'site_description', 'conf_value' => '深圳市名扬科技专注企业数字化建设，提供云平台、智能应用与行业解决方案，助力企业稳健增长。', 'conf_desc' => '站点描述', 'conf_sort' => 40],
            ['conf_group' => 'basic', 'conf_key' => 'company_full_name', 'conf_value' => '深圳市名扬科技有限公司', 'conf_desc' => '公司全称', 'conf_sort' => 50],
            ['conf_group' => 'contact', 'conf_key' => 'phone', 'conf_value' => '0755-88886666', 'conf_desc' => '联系电话', 'conf_sort' => 10],
            ['conf_group' => 'contact', 'conf_key' => 'email', 'conf_value' => 'contact@mingyang.tech', 'conf_desc' => '邮箱地址', 'conf_sort' => 20],
            ['conf_group' => 'contact', 'conf_key' => 'address', 'conf_value' => '深圳市南山区科技园南区科苑路', 'conf_desc' => '公司地址', 'conf_sort' => 30],
            ['conf_group' => 'social', 'conf_key' => 'wechat', 'conf_value' => 'mingyang_tech', 'conf_desc' => '微信公众号', 'conf_sort' => 10],
        ];

        foreach ($items as $item) {
            SiteConfig::query()->updateOrCreate(
                ['conf_key' => $item['conf_key']],
                [
                    'conf_group' => $item['conf_group'],
                    'conf_value' => $item['conf_value'],
                    'conf_desc' => $item['conf_desc'],
                    'input_type' => strlen($item['conf_value']) > 40 ? 'textarea' : 'text',
                    'conf_sort' => $item['conf_sort'],
                ]
            );
        }
    }
}
