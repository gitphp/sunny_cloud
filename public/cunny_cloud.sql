/*
 Navicat Premium Dump SQL

 Source Server         : local_Eserver
 Source Server Type    : MySQL
 Source Server Version : 80025 (8.0.25)
 Source Host           : localhost:3306
 Source Schema         : cunny_cloud

 Target Server Type    : MySQL
 Target Server Version : 80025 (8.0.25)
 File Encoding         : 65001

 Date: 26/07/2026 22:28:35
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ad_positions
-- ----------------------------
DROP TABLE IF EXISTS `ad_positions`;
CREATE TABLE `ad_positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `ad_title` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '广告标题',
  `subtitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '广告副标题/描述',
  `cover_url` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '广告封面图URL（主图）',
  `cover_mobile` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '移动端封面图（适配不同尺寸）',
  `cover_thumb` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '缩略图（列表页展示）',
  `video_url` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '视频广告URL（支持视频广告）',
  `link_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '跳转类型：1=站内链接，2=站外链接，3=小程序，4=无跳转（纯展示）',
  `link_url` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '跳转链接地址',
  `link_params` json DEFAULT NULL COMMENT '跳转参数，如：{"utm_source":"home","utm_medium":"banner"}',
  `app_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '小程序AppId（link_type=3时使用）',
  `app_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '小程序路径（link_type=3时使用）',
  `position_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '广告位编码，如：home_banner_top、home_sidebar_1',
  `platform` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '投放平台：1=全部，2=PC端，3=移动端，4=小程序',
  `device_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '设备类型：1=全部，2=iOS，3=Android，4=其他',
  `target_user_type` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '用户定向：0=全部用户，1=新用户，2=老用户，3=VIP用户，4=指定用户组',
  `target_user_group_ids` json DEFAULT NULL COMMENT '目标用户组ID列表，如：[1,2,3]',
  `target_region` json DEFAULT NULL COMMENT '目标地区，如：{"province":["广东","浙江"],"city":["深圳","杭州"]}',
  `start_time` datetime NOT NULL COMMENT '投放开始时间',
  `end_time` datetime NOT NULL COMMENT '投放结束时间',
  `show_time_type` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '展示时间类型：0=全天，1=自定义时间段',
  `time_slots` json DEFAULT NULL COMMENT '自定义时间段，如：[{"start":"09:00","end":"12:00"},{"start":"14:00","end":"18:00"}]',
  `weekdays` json DEFAULT NULL COMMENT '投放星期，如：[1,2,3,4,5] 表示周一至周五',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序权重（值越大越靠前）',
  `display_frequency` int unsigned NOT NULL DEFAULT '1' COMMENT '展示频率：1=每人每天1次，2=每人每小时1次，3=无限次',
  `daily_impression_limit` int unsigned NOT NULL DEFAULT '0' COMMENT '每日展示次数限制（全局）',
  `daily_click_limit` int unsigned NOT NULL DEFAULT '0' COMMENT '每日点击次数限制（全局）',
  `budget` decimal(12,2) DEFAULT NULL COMMENT '预算金额（CPM/CPC模式使用）',
  `cost_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '计费方式：1=CPM，2=CPC，3=CPT，4=CPA',
  `bid_price` decimal(10,2) DEFAULT NULL COMMENT '出价金额（CPM/CPC时使用）',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1=草稿，2=待审核，3=审核通过，4=投放中，5=已结束，6=已暂停，7=审核驳回，8=已下线',
  `audit_status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '审核状态：0=未提交，1=待审核，2=审核通过，3=审核驳回',
  `reviewer_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '审核人ID',
  `reviewed_at` datetime DEFAULT NULL COMMENT '审核时间',
  `reject_reason` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '驳回原因',
  `impression_count` bigint unsigned NOT NULL DEFAULT '0' COMMENT '展示次数',
  `click_count` bigint unsigned NOT NULL DEFAULT '0' COMMENT '点击次数',
  `click_rate` decimal(6,4) NOT NULL DEFAULT '0.0000' COMMENT '点击率（CTR）',
  `daily_stats` json DEFAULT NULL COMMENT '日统计数据缓存，如：{"2026-07-23":{"impression":1000,"click":50}}',
  `created_by` bigint unsigned NOT NULL DEFAULT '0' COMMENT '创建人ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime DEFAULT NULL COMMENT '软删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_position_code` (`position_code`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE,
  KEY `idx_start_end_time` (`start_time`,`end_time`) USING BTREE,
  KEY `idx_platform` (`platform`) USING BTREE,
  KEY `idx_sort` (`sort`) USING BTREE,
  KEY `idx_created_at` (`created_at`) USING BTREE,
  KEY `idx_deleted_at` (`deleted_at`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733863055423260 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='广告位主表';

-- ----------------------------
-- Records of ad_positions
-- ----------------------------
BEGIN;
INSERT INTO `ad_positions` (`id`, `ad_title`, `subtitle`, `cover_url`, `cover_mobile`, `cover_thumb`, `video_url`, `link_type`, `link_url`, `link_params`, `app_id`, `app_path`, `position_code`, `platform`, `device_type`, `target_user_type`, `target_user_group_ids`, `target_region`, `start_time`, `end_time`, `show_time_type`, `time_slots`, `weekdays`, `sort`, `display_frequency`, `daily_impression_limit`, `daily_click_limit`, `budget`, `cost_type`, `bid_price`, `status`, `audit_status`, `reviewer_id`, `reviewed_at`, `reject_reason`, `impression_count`, `click_count`, `click_rate`, `daily_stats`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863055423246, '2026年企业数字化转型峰会', '携手行业大咖，共话数字化未来', '/uploads/ad/banner_digital_summit.jpg', '/uploads/ad/mobile/banner_digital_summit.jpg', '/uploads/ad/thumb/banner_digital_summit.jpg', '', 2, 'https://www.example.com/event/digital-summit-2026', '{\"utm_medium\": \"banner_top\", \"utm_source\": \"home\", \"utm_campaign\": \"digital_summit\"}', '', '', 'home_banner_top', 1, 1, 0, NULL, '{\"city\": [\"深圳\", \"广州\", \"北京\", \"上海\", \"杭州\"], \"province\": [\"广东\", \"北京\", \"上海\", \"浙江\"]}', '2026-07-01 00:00:00', '2026-09-30 23:59:59', 0, NULL, '[1, 2, 3, 4, 5, 6, 7]', 100, 1, 0, 0, NULL, 1, NULL, 4, 2, 920733860755423002, '2026-07-01 09:00:00', '', 12580, 368, 0.0293, '{\"2026-07-01\": {\"click\": 15, \"impression\": 520}, \"2026-07-02\": {\"click\": 12, \"impression\": 480}, \"2026-07-03\": {\"click\": 18, \"impression\": 550}}', 920733860755423002, '2026-06-25 10:00:00', '2026-07-23 09:43:18', NULL);
INSERT INTO `ad_positions` (`id`, `ad_title`, `subtitle`, `cover_url`, `cover_mobile`, `cover_thumb`, `video_url`, `link_type`, `link_url`, `link_params`, `app_id`, `app_path`, `position_code`, `platform`, `device_type`, `target_user_type`, `target_user_group_ids`, `target_region`, `start_time`, `end_time`, `show_time_type`, `time_slots`, `weekdays`, `sort`, `display_frequency`, `daily_impression_limit`, `daily_click_limit`, `budget`, `cost_type`, `bid_price`, `status`, `audit_status`, `reviewer_id`, `reviewed_at`, `reject_reason`, `impression_count`, `click_count`, `click_rate`, `daily_stats`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863055423247, '企业官网全新升级 2.0 版本发布', '更流畅的体验，更强大的功能', '/uploads/ad/banner_v2_pc.jpg', '/uploads/ad/mobile/banner_v2_mobile.jpg', '/uploads/ad/thumb/banner_v2_thumb.jpg', '', 1, '/product/version-2-0', '{\"utm_medium\": \"banner_top_mobile\", \"utm_source\": \"home\", \"utm_campaign\": \"v2_launch\"}', '', '', 'home_banner_top', 3, 1, 0, NULL, NULL, '2026-07-15 00:00:00', '2026-08-31 23:59:59', 0, NULL, '[1, 2, 3, 4, 5, 6, 7]', 95, 1, 0, 0, NULL, 1, NULL, 4, 2, 920733860755423002, '2026-07-14 14:30:00', '', 8560, 245, 0.0286, NULL, 920733860755423003, '2026-07-10 09:00:00', '2026-07-23 09:43:18', NULL);
INSERT INTO `ad_positions` (`id`, `ad_title`, `subtitle`, `cover_url`, `cover_mobile`, `cover_thumb`, `video_url`, `link_type`, `link_url`, `link_params`, `app_id`, `app_path`, `position_code`, `platform`, `device_type`, `target_user_type`, `target_user_group_ids`, `target_region`, `start_time`, `end_time`, `show_time_type`, `time_slots`, `weekdays`, `sort`, `display_frequency`, `daily_impression_limit`, `daily_click_limit`, `budget`, `cost_type`, `bid_price`, `status`, `audit_status`, `reviewer_id`, `reviewed_at`, `reject_reason`, `impression_count`, `click_count`, `click_rate`, `daily_stats`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863055423248, '新人专享大礼包', '注册即送价值 500 元优惠券', '/uploads/ad/banner_new_user.jpg', '/uploads/ad/mobile/banner_new_user.jpg', '/uploads/ad/thumb/banner_new_user.jpg', '', 1, '/user/register', '{\"utm_medium\": \"banner_top\", \"utm_source\": \"home\", \"utm_campaign\": \"new_user_gift\"}', '', '', 'home_banner_top', 1, 1, 1, NULL, NULL, '2026-07-01 00:00:00', '2026-08-15 23:59:59', 0, NULL, '[1, 2, 3, 4, 5, 6, 7]', 90, 2, 0, 0, NULL, 1, NULL, 4, 2, 920733860755423002, '2026-07-01 08:00:00', '', 6320, 189, 0.0299, NULL, 920733860755423003, '2026-06-28 16:00:00', '2026-07-23 09:43:18', NULL);
INSERT INTO `ad_positions` (`id`, `ad_title`, `subtitle`, `cover_url`, `cover_mobile`, `cover_thumb`, `video_url`, `link_type`, `link_url`, `link_params`, `app_id`, `app_path`, `position_code`, `platform`, `device_type`, `target_user_type`, `target_user_group_ids`, `target_region`, `start_time`, `end_time`, `show_time_type`, `time_slots`, `weekdays`, `sort`, `display_frequency`, `daily_impression_limit`, `daily_click_limit`, `budget`, `cost_type`, `bid_price`, `status`, `audit_status`, `reviewer_id`, `reviewed_at`, `reject_reason`, `impression_count`, `click_count`, `click_rate`, `daily_stats`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863055423249, '企业级解决方案免费试用', '30天全功能体验，助力企业高效运营', '/uploads/ad/sidebar_free_trial.jpg', '', '/uploads/ad/thumb/sidebar_free_trial.jpg', '', 2, 'https://www.example.com/trial', '{\"utm_medium\": \"sidebar\", \"utm_source\": \"home\", \"utm_campaign\": \"free_trial\"}', '', '', 'home_sidebar', 1, 1, 0, NULL, '{\"province\": [\"广东\", \"北京\", \"上海\"]}', '2026-07-01 00:00:00', '2026-12-31 23:59:59', 1, '[{\"end\": \"18:00\", \"start\": \"09:00\"}]', '[1, 2, 3, 4, 5]', 80, 1, 1000, 50, NULL, 1, NULL, 4, 2, 920733860755423002, '2026-07-01 10:00:00', '', 3240, 98, 0.0302, NULL, 920733860755423004, '2026-06-30 11:00:00', '2026-07-23 09:43:18', NULL);
INSERT INTO `ad_positions` (`id`, `ad_title`, `subtitle`, `cover_url`, `cover_mobile`, `cover_thumb`, `video_url`, `link_type`, `link_url`, `link_params`, `app_id`, `app_path`, `position_code`, `platform`, `device_type`, `target_user_type`, `target_user_group_ids`, `target_region`, `start_time`, `end_time`, `show_time_type`, `time_slots`, `weekdays`, `sort`, `display_frequency`, `daily_impression_limit`, `daily_click_limit`, `budget`, `cost_type`, `bid_price`, `status`, `audit_status`, `reviewer_id`, `reviewed_at`, `reject_reason`, `impression_count`, `click_count`, `click_rate`, `daily_stats`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863055423250, '合作伙伴招募计划', '诚邀优质合作伙伴，共享万亿市场', '/uploads/ad/bottom_partner.jpg', '/uploads/ad/mobile/bottom_partner.jpg', '/uploads/ad/thumb/bottom_partner.jpg', '', 2, 'https://www.example.com/partner', '{\"utm_medium\": \"bottom\", \"utm_source\": \"home\", \"utm_campaign\": \"partner_recruit\"}', '', '', 'home_bottom', 1, 1, 0, NULL, '{\"province\": [\"广东\", \"北京\", \"上海\", \"浙江\"]}', '2026-07-01 00:00:00', '2026-10-31 23:59:59', 0, NULL, '[1, 2, 3, 4, 5, 6, 7]', 70, 3, 0, 0, NULL, 1, NULL, 4, 2, 920733860755423002, '2026-07-01 09:00:00', '', 21500, 645, 0.0300, '{\"2026-07-01\": {\"click\": 36, \"impression\": 1200}, \"2026-07-02\": {\"click\": 33, \"impression\": 1100}}', 920733860755423004, '2026-06-25 14:00:00', '2026-07-23 09:43:18', NULL);
INSERT INTO `ad_positions` (`id`, `ad_title`, `subtitle`, `cover_url`, `cover_mobile`, `cover_thumb`, `video_url`, `link_type`, `link_url`, `link_params`, `app_id`, `app_path`, `position_code`, `platform`, `device_type`, `target_user_type`, `target_user_group_ids`, `target_region`, `start_time`, `end_time`, `show_time_type`, `time_slots`, `weekdays`, `sort`, `display_frequency`, `daily_impression_limit`, `daily_click_limit`, `budget`, `cost_type`, `bid_price`, `status`, `audit_status`, `reviewer_id`, `reviewed_at`, `reject_reason`, `impression_count`, `click_count`, `click_rate`, `daily_stats`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863055423251, '重要通知：系统升级维护公告', '2026年7月25日 02:00-06:00 暂停服务', '/uploads/ad/popup_maintenance.jpg', '/uploads/ad/mobile/popup_maintenance.jpg', '/uploads/ad/thumb/popup_maintenance.jpg', '', 4, '', NULL, '', '', 'home_popup', 1, 1, 0, NULL, NULL, '2026-07-20 00:00:00', '2026-07-25 23:59:59', 1, '[{\"end\": \"23:59\", \"start\": \"00:00\"}]', '[1, 2, 3, 4, 5, 6, 7]', 60, 2, 0, 0, NULL, 1, NULL, 4, 2, 920733860755423002, '2026-07-19 17:00:00', '', 9800, 294, 0.0300, NULL, 920733860755423002, '2026-07-18 09:00:00', '2026-07-23 09:43:18', NULL);
INSERT INTO `ad_positions` (`id`, `ad_title`, `subtitle`, `cover_url`, `cover_mobile`, `cover_thumb`, `video_url`, `link_type`, `link_url`, `link_params`, `app_id`, `app_path`, `position_code`, `platform`, `device_type`, `target_user_type`, `target_user_group_ids`, `target_region`, `start_time`, `end_time`, `show_time_type`, `time_slots`, `weekdays`, `sort`, `display_frequency`, `daily_impression_limit`, `daily_click_limit`, `budget`, `cost_type`, `bid_price`, `status`, `audit_status`, `reviewer_id`, `reviewed_at`, `reject_reason`, `impression_count`, `click_count`, `click_rate`, `daily_stats`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863055423252, '技术干货：PHP 8 新特性详解', '深入剖析 PHP 8 的核心改进与最佳实践', '/uploads/ad/inner_php8.jpg', '/uploads/ad/mobile/inner_php8.jpg', '/uploads/ad/thumb/inner_php8.jpg', '', 1, '/article/php8-new-features', '{\"utm_medium\": \"banner_top\", \"utm_source\": \"inner\", \"utm_campaign\": \"php8\"}', '', '', 'inner_banner_top', 1, 1, 0, NULL, NULL, '2026-07-01 00:00:00', '2026-08-31 23:59:59', 0, NULL, '[1, 2, 3, 4, 5, 6, 7]', 50, 1, 0, 0, NULL, 1, NULL, 4, 2, 920733860755423002, '2026-07-01 10:00:00', '', 4200, 126, 0.0300, NULL, 920733860755423003, '2026-06-28 10:00:00', '2026-07-23 09:43:18', NULL);
INSERT INTO `ad_positions` (`id`, `ad_title`, `subtitle`, `cover_url`, `cover_mobile`, `cover_thumb`, `video_url`, `link_type`, `link_url`, `link_params`, `app_id`, `app_path`, `position_code`, `platform`, `device_type`, `target_user_type`, `target_user_group_ids`, `target_region`, `start_time`, `end_time`, `show_time_type`, `time_slots`, `weekdays`, `sort`, `display_frequency`, `daily_impression_limit`, `daily_click_limit`, `budget`, `cost_type`, `bid_price`, `status`, `audit_status`, `reviewer_id`, `reviewed_at`, `reject_reason`, `impression_count`, `click_count`, `click_rate`, `daily_stats`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863055423253, '扫描二维码关注公众号', '获取更多技术干货和行业资讯', '/uploads/ad/float_qrcode.jpg', '/uploads/ad/mobile/float_qrcode.jpg', '/uploads/ad/thumb/float_qrcode.jpg', '', 4, '', NULL, '', '', 'inner_article_float', 1, 1, 0, NULL, NULL, '2026-07-01 00:00:00', '2026-12-31 23:59:59', 0, NULL, '[1, 2, 3, 4, 5, 6, 7]', 40, 3, 0, 0, NULL, 1, NULL, 4, 2, 920733860755423002, '2026-07-01 08:00:00', '', 15000, 450, 0.0300, NULL, 920733860755423003, '2026-06-30 09:00:00', '2026-07-23 09:43:18', NULL);
INSERT INTO `ad_positions` (`id`, `ad_title`, `subtitle`, `cover_url`, `cover_mobile`, `cover_thumb`, `video_url`, `link_type`, `link_url`, `link_params`, `app_id`, `app_path`, `position_code`, `platform`, `device_type`, `target_user_type`, `target_user_group_ids`, `target_region`, `start_time`, `end_time`, `show_time_type`, `time_slots`, `weekdays`, `sort`, `display_frequency`, `daily_impression_limit`, `daily_click_limit`, `budget`, `cost_type`, `bid_price`, `status`, `audit_status`, `reviewer_id`, `reviewed_at`, `reject_reason`, `impression_count`, `click_count`, `click_rate`, `daily_stats`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863055423254, '新品发布：企业级 AI 智能平台', '赋能企业智能化转型，提升核心竞争力', '/uploads/ad/product_ai_platform.jpg', '/uploads/ad/mobile/product_ai_platform.jpg', '/uploads/ad/thumb/product_ai_platform.jpg', '', 1, '/product/ai-platform', '{\"utm_medium\": \"banner\", \"utm_source\": \"product\", \"utm_campaign\": \"ai_platform\"}', '', '', 'product_banner', 1, 1, 0, NULL, NULL, '2026-07-15 00:00:00', '2026-09-30 23:59:59', 0, NULL, '[1, 2, 3, 4, 5, 6, 7]', 110, 3, 0, 0, NULL, 1, NULL, 4, 2, 920733860755423002, '2026-07-15 09:00:00', '', 3860, 116, 0.0301, NULL, 920733860755423003, '2026-07-14 10:00:00', '2026-07-23 09:43:18', NULL);
INSERT INTO `ad_positions` (`id`, `ad_title`, `subtitle`, `cover_url`, `cover_mobile`, `cover_thumb`, `video_url`, `link_type`, `link_url`, `link_params`, `app_id`, `app_path`, `position_code`, `platform`, `device_type`, `target_user_type`, `target_user_group_ids`, `target_region`, `start_time`, `end_time`, `show_time_type`, `time_slots`, `weekdays`, `sort`, `display_frequency`, `daily_impression_limit`, `daily_click_limit`, `budget`, `cost_type`, `bid_price`, `status`, `audit_status`, `reviewer_id`, `reviewed_at`, `reject_reason`, `impression_count`, `click_count`, `click_rate`, `daily_stats`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863055423255, '待发布：年度品牌宣传片', '敬请期待，即将上线', '/uploads/ad/draft_brand_film.jpg', '/uploads/ad/mobile/draft_brand_film.jpg', '/uploads/ad/thumb/draft_brand_film.jpg', '', 2, 'https://www.example.com/brand', '{\"utm_medium\": \"banner\", \"utm_source\": \"draft\"}', '', '', 'home_banner_top', 1, 1, 0, NULL, NULL, '2026-08-01 00:00:00', '2026-10-31 23:59:59', 0, NULL, '[1, 2, 3, 4, 5, 6, 7]', 20, 1, 0, 0, NULL, 1, NULL, 1, 0, 0, NULL, '', 0, 0, 0.0000, NULL, 920733860755423003, '2026-07-20 14:00:00', '2026-07-23 09:43:18', NULL);
INSERT INTO `ad_positions` (`id`, `ad_title`, `subtitle`, `cover_url`, `cover_mobile`, `cover_thumb`, `video_url`, `link_type`, `link_url`, `link_params`, `app_id`, `app_path`, `position_code`, `platform`, `device_type`, `target_user_type`, `target_user_group_ids`, `target_region`, `start_time`, `end_time`, `show_time_type`, `time_slots`, `weekdays`, `sort`, `display_frequency`, `daily_impression_limit`, `daily_click_limit`, `budget`, `cost_type`, `bid_price`, `status`, `audit_status`, `reviewer_id`, `reviewed_at`, `reject_reason`, `impression_count`, `click_count`, `click_rate`, `daily_stats`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863055423256, '暑期促销活动', '全场商品 8 折起', '/uploads/ad/rejected_summer_sale.jpg', '/uploads/ad/mobile/rejected_summer_sale.jpg', '/uploads/ad/thumb/rejected_summer_sale.jpg', '', 2, 'https://www.example.com/summer-sale', '{\"utm_medium\": \"banner\", \"utm_source\": \"home\", \"utm_campaign\": \"summer_sale\"}', '', '', 'home_banner_top', 1, 1, 0, NULL, NULL, '2026-07-01 00:00:00', '2026-07-31 23:59:59', 0, NULL, '[1, 2, 3, 4, 5, 6, 7]', 0, 1, 0, 0, NULL, 1, NULL, 7, 3, 920733860755423002, '2026-07-01 11:00:00', '广告素材涉及版权问题，请替换图片后重新提交审核', 0, 0, 0.0000, NULL, 920733860755423003, '2026-06-28 16:00:00', '2026-07-23 09:43:18', NULL);
INSERT INTO `ad_positions` (`id`, `ad_title`, `subtitle`, `cover_url`, `cover_mobile`, `cover_thumb`, `video_url`, `link_type`, `link_url`, `link_params`, `app_id`, `app_path`, `position_code`, `platform`, `device_type`, `target_user_type`, `target_user_group_ids`, `target_region`, `start_time`, `end_time`, `show_time_type`, `time_slots`, `weekdays`, `sort`, `display_frequency`, `daily_impression_limit`, `daily_click_limit`, `budget`, `cost_type`, `bid_price`, `status`, `audit_status`, `reviewer_id`, `reviewed_at`, `reject_reason`, `impression_count`, `click_count`, `click_rate`, `daily_stats`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863055423257, '限时优惠：企业版年度会员 5 折', '仅限前 100 名客户，先到先得', '/uploads/ad/paused_enterprise_discount.jpg', '/uploads/ad/mobile/paused_enterprise_discount.jpg', '/uploads/ad/thumb/paused_enterprise_discount.jpg', '', 2, 'https://www.example.com/enterprise-discount', '{\"utm_medium\": \"sidebar\", \"utm_source\": \"home\", \"utm_campaign\": \"enterprise_discount\"}', '', '', 'home_sidebar', 1, 1, 0, NULL, NULL, '2026-07-01 00:00:00', '2026-08-31 23:59:59', 0, NULL, '[1, 2, 3, 4, 5, 6, 7]', 0, 1, 0, 0, NULL, 1, NULL, 6, 2, 920733860755423002, '2026-07-01 09:00:00', '', 1280, 38, 0.0297, NULL, 920733860755423004, '2026-06-30 14:00:00', '2026-07-23 09:43:18', NULL);
INSERT INTO `ad_positions` (`id`, `ad_title`, `subtitle`, `cover_url`, `cover_mobile`, `cover_thumb`, `video_url`, `link_type`, `link_url`, `link_params`, `app_id`, `app_path`, `position_code`, `platform`, `device_type`, `target_user_type`, `target_user_group_ids`, `target_region`, `start_time`, `end_time`, `show_time_type`, `time_slots`, `weekdays`, `sort`, `display_frequency`, `daily_impression_limit`, `daily_click_limit`, `budget`, `cost_type`, `bid_price`, `status`, `audit_status`, `reviewer_id`, `reviewed_at`, `reject_reason`, `impression_count`, `click_count`, `click_rate`, `daily_stats`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863055423258, '618 年中大促活动', '全场满减，限时抢购', '/uploads/ad/ended_618_sale.jpg', '/uploads/ad/mobile/ended_618_sale.jpg', '/uploads/ad/thumb/ended_618_sale.jpg', '', 2, 'https://www.example.com/618-sale', '{\"utm_medium\": \"banner_top\", \"utm_source\": \"home\", \"utm_campaign\": \"618_sale\"}', '', '', 'home_banner_top', 1, 1, 0, NULL, NULL, '2026-06-01 00:00:00', '2026-06-20 23:59:59', 0, NULL, '[1, 2, 3, 4, 5, 6, 7]', 0, 1, 0, 0, NULL, 1, NULL, 5, 2, 920733860755423002, '2026-06-01 08:00:00', '', 25600, 768, 0.0300, NULL, 920733860755423003, '2026-05-28 10:00:00', '2026-07-23 09:43:18', NULL);
INSERT INTO `ad_positions` (`id`, `ad_title`, `subtitle`, `cover_url`, `cover_mobile`, `cover_thumb`, `video_url`, `link_type`, `link_url`, `link_params`, `app_id`, `app_path`, `position_code`, `platform`, `device_type`, `target_user_type`, `target_user_group_ids`, `target_region`, `start_time`, `end_time`, `show_time_type`, `time_slots`, `weekdays`, `sort`, `display_frequency`, `daily_impression_limit`, `daily_click_limit`, `budget`, `cost_type`, `bid_price`, `status`, `audit_status`, `reviewer_id`, `reviewed_at`, `reject_reason`, `impression_count`, `click_count`, `click_rate`, `daily_stats`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863055423259, '临时测试广告', '', '', '', '', '', 1, '', NULL, '', '', 'tmp_test_1785033438', 1, 1, 0, '[]', NULL, '2026-07-26 00:00:00', '2026-08-26 00:00:00', 0, '[]', '[]', 0, 1, 0, 0, NULL, 1, NULL, 3, 2, 0, '2026-07-26 02:37:18', '', 0, 0, 0.0000, NULL, 0, '2026-07-26 02:37:18', '2026-07-26 02:37:18', '2026-07-26 02:37:18');
COMMIT;

-- ----------------------------
-- Table structure for ad_slots
-- ----------------------------
DROP TABLE IF EXISTS `ad_slots`;
CREATE TABLE `ad_slots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slot_code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '广告位编码，如：home_banner_top',
  `slot_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '广告位名称，如：首页顶部轮播图',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '广告位描述',
  `width` int unsigned NOT NULL DEFAULT '0' COMMENT '广告位宽度（像素）',
  `height` int unsigned NOT NULL DEFAULT '0' COMMENT '广告位高度（像素）',
  `max_items` int unsigned NOT NULL DEFAULT '1' COMMENT '最大展示数量',
  `is_system` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否系统预设：0=否，1=是',
  `slot_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0=禁用，1=启用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_code` (`slot_code`) USING BTREE,
  KEY `idx_status` (`slot_status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733863755423259 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='广告位位置定义表';

-- ----------------------------
-- Records of ad_slots
-- ----------------------------
BEGIN;
INSERT INTO `ad_slots` (`id`, `slot_code`, `slot_name`, `description`, `width`, `height`, `max_items`, `is_system`, `slot_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863755423246, 'home_banner_top', '首页顶部轮播图', '首页顶部焦点图轮播区域，用于展示品牌形象、重大活动或核心产品，支持多图轮播', 1920, 600, 5, 1, 1, '2026-07-23 09:21:11', '2026-07-23 09:21:11', NULL);
INSERT INTO `ad_slots` (`id`, `slot_code`, `slot_name`, `description`, `width`, `height`, `max_items`, `is_system`, `slot_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863755423247, 'home_banner_top_mobile', '首页顶部轮播图（移动端）', '移动端首页顶部焦点图轮播区域，适配手机屏幕尺寸', 750, 400, 5, 1, 1, '2026-07-23 09:21:11', '2026-07-23 09:21:11', NULL);
INSERT INTO `ad_slots` (`id`, `slot_code`, `slot_name`, `description`, `width`, `height`, `max_items`, `is_system`, `slot_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863755423248, 'home_sidebar', '首页侧边栏广告位', '首页右侧边栏广告位，适合展示活动推广、产品推荐或联系方式', 300, 600, 2, 1, 1, '2026-07-23 09:21:11', '2026-07-23 09:21:11', NULL);
INSERT INTO `ad_slots` (`id`, `slot_code`, `slot_name`, `description`, `width`, `height`, `max_items`, `is_system`, `slot_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863755423249, 'home_bottom', '首页底部横幅', '首页底部通栏横幅广告位，适合展示合作伙伴、友情链接或品牌宣传', 1920, 200, 3, 1, 1, '2026-07-23 09:21:11', '2026-07-23 09:21:11', NULL);
INSERT INTO `ad_slots` (`id`, `slot_code`, `slot_name`, `description`, `width`, `height`, `max_items`, `is_system`, `slot_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863755423250, 'home_popup', '首页弹窗广告', '进入首页时自动弹出的广告窗口，适合重要活动通知或用户引导', 600, 450, 1, 1, 1, '2026-07-23 09:21:11', '2026-07-23 09:21:11', NULL);
INSERT INTO `ad_slots` (`id`, `slot_code`, `slot_name`, `description`, `width`, `height`, `max_items`, `is_system`, `slot_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863755423251, 'inner_banner_top', '内页顶部横幅', '内页（文章详情页、产品页等）顶部通栏横幅广告位', 1920, 300, 3, 1, 1, '2026-07-23 09:21:11', '2026-07-23 09:21:11', NULL);
INSERT INTO `ad_slots` (`id`, `slot_code`, `slot_name`, `description`, `width`, `height`, `max_items`, `is_system`, `slot_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863755423252, 'inner_sidebar', '内页侧边栏广告位', '内页右侧边栏广告位，适合展示相关推荐、热门产品或联系表单', 300, 500, 2, 1, 1, '2026-07-23 09:21:11', '2026-07-23 09:21:11', NULL);
INSERT INTO `ad_slots` (`id`, `slot_code`, `slot_name`, `description`, `width`, `height`, `max_items`, `is_system`, `slot_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863755423253, 'inner_article_float', '文章详情页悬浮广告', '文章详情页底部悬浮广告位，不干扰阅读体验，适合引导关注或下载', 750, 120, 1, 1, 1, '2026-07-23 09:21:11', '2026-07-23 09:21:11', NULL);
INSERT INTO `ad_slots` (`id`, `slot_code`, `slot_name`, `description`, `width`, `height`, `max_items`, `is_system`, `slot_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863755423254, 'product_banner', '产品列表页顶部横幅', '产品列表页顶部的品牌/活动宣传横幅，可突出核心产品线', 1920, 350, 3, 1, 1, '2026-07-23 09:21:11', '2026-07-23 09:21:11', NULL);
INSERT INTO `ad_slots` (`id`, `slot_code`, `slot_name`, `description`, `width`, `height`, `max_items`, `is_system`, `slot_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863755423255, 'product_detail_bottom', '产品详情页底部推荐', '产品详情页底部推荐广告位，展示相关产品或配套服务', 1200, 200, 4, 1, 1, '2026-07-23 09:21:11', '2026-07-23 09:21:11', NULL);
INSERT INTO `ad_slots` (`id`, `slot_code`, `slot_name`, `description`, `width`, `height`, `max_items`, `is_system`, `slot_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863755423256, 'about_us_banner', '关于我们页横幅', '关于我们页面顶部横幅，用于展示企业使命、愿景或宣传视频', 1920, 450, 2, 1, 1, '2026-07-23 09:21:11', '2026-07-23 09:21:11', NULL);
INSERT INTO `ad_slots` (`id`, `slot_code`, `slot_name`, `description`, `width`, `height`, `max_items`, `is_system`, `slot_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863755423257, 'job_banner', '招聘页顶部横幅', '招聘页面顶部横幅，展示企业雇主品牌和团队文化', 1920, 350, 2, 1, 1, '2026-07-23 09:21:11', '2026-07-23 09:21:11', NULL);
INSERT INTO `ad_slots` (`id`, `slot_code`, `slot_name`, `description`, `width`, `height`, `max_items`, `is_system`, `slot_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863755423258, 'tmp_test_1785033438', '临时测试位', '', 200, 100, 1, 0, 1, '2026-07-26 02:37:18', '2026-07-26 02:37:18', '2026-07-26 02:37:18');
COMMIT;

-- ----------------------------
-- Table structure for article_category
-- ----------------------------
DROP TABLE IF EXISTS `article_category`;
CREATE TABLE `article_category` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '父级ID，0表示顶级',
  `cat_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `cat_url` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT 'URL别名，如：company-news',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '分类描述',
  `cat_sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序权重',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0=禁用，1=启用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_parent_id` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733863034423263 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='文章分类表';

-- ----------------------------
-- Records of article_category
-- ----------------------------
BEGIN;
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423246, 0, '公司新闻', 'company-news', '发布公司的最新动态、重大事件和公告', 100, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423247, 0, '行业动态', 'industry-news', '跟踪行业发展趋势、政策法规和市场变化', 90, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423248, 0, '产品资讯', 'product-news', '介绍产品更新、功能迭代和使用教程', 80, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423249, 0, '技术分享', 'tech-sharing', '分享技术干货、开发经验和最佳实践', 70, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423250, 0, '招聘信息', 'job-info', '发布招聘职位、人才需求和团队文化', 60, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423251, 0, '客户案例', 'customer-cases', '展示客户成功案例和合作故事', 50, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423252, 0, '企业荣誉', 'company-honor', '展示企业获得的资质、认证和奖项', 40, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423253, 920733863034423246, '企业公告', 'company-announcement', '发布企业重要公告和通知', 10, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423254, 920733863034423246, '企业活动', 'company-event', '报道企业举办的会议、培训和团建活动', 9, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423255, 920733863034423246, '企业荣誉', 'company-honor', '展示企业获得的荣誉和资质认证', 8, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423256, 920733863034423247, '行业政策', 'industry-policy', '解读国家和地方相关政策法规', 10, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423257, 920733863034423247, '市场趋势', 'market-trend', '分析市场变化和行业发展趋势', 9, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423258, 920733863034423248, '产品发布', 'product-release', '介绍新产品发布和重大版本更新', 10, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423259, 920733863034423248, '使用教程', 'product-tutorial', '提供产品功能的使用方法和操作指南', 9, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423260, 920733863034423249, '后端开发', 'backend-dev', '分享后端技术架构、数据库和性能优化经验', 10, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423261, 920733863034423249, '前端开发', 'frontend-dev', '分享前端框架、UI设计和用户体验技巧', 9, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
INSERT INTO `article_category` (`id`, `parent_id`, `cat_name`, `cat_url`, `description`, `cat_sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034423262, 920733863034423249, '运维安全', 'devops-security', '分享运维部署、安全防护和系统监控经验', 8, 1, '2026-07-23 09:19:35', '2026-07-23 09:19:35', NULL);
COMMIT;

-- ----------------------------
-- Table structure for articles
-- ----------------------------
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID（雪花ID或自增）',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '文章标题',
  `subtitle` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '副标题/摘要',
  `art_cover` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '封面图URL（支持多图用JSON）',
  `art_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '文章正文内容（富文本/Markdown）',
  `content_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '内容类型：1=富文本，2=Markdown，3=纯文本',
  `summary` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '文章摘要（自动截取或手动填写）',
  `category_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '分类ID（关联 categories 表）',
  `tag_ids` json DEFAULT NULL COMMENT '标签ID列表，如 [1,2,3]（关联 tags 表）',
  `author_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '作者用户ID（关联 users 表）',
  `author_name` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '作者姓名（冗余字段，防止用户改名）',
  `source` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '文章来源（如：原创/转载/翻译）',
  `source_url` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '原文链接（转载时使用）',
  `art_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1=草稿，2=待审核，3=审核通过，4=已发布，5=已下线，6=审核驳回，7=回收站',
  `is_top` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否置顶：0=否，1=是',
  `is_original` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '是否原创：0=否，1=是',
  `is_commentable` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '是否允许评论：0=否，1=是',
  `seo_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT 'SEO标题（为空时取 title）',
  `seo_keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT 'SEO关键词',
  `seo_description` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT 'SEO描述',
  `extra_fields` json DEFAULT NULL COMMENT '扩展字段（如：视频链接、下载链接、相关推荐等）',
  `view_count` int unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `like_count` int unsigned NOT NULL DEFAULT '0' COMMENT '点赞量',
  `collect_count` int unsigned NOT NULL DEFAULT '0' COMMENT '收藏量',
  `share_count` int unsigned NOT NULL DEFAULT '0' COMMENT '分享量',
  `comment_count` int unsigned NOT NULL DEFAULT '0' COMMENT '评论量',
  `published_at` datetime DEFAULT NULL COMMENT '发布时间（状态变为“已发布”时记录）',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime DEFAULT NULL COMMENT '软删除时间',
  `reviewer_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '审核人ID（关联 users 表）',
  `reviewed_at` datetime DEFAULT NULL COMMENT '审核时间',
  `reject_reason` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT '驳回原因（审核驳回时填写）',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_author_id` (`author_id`) USING BTREE,
  KEY `idx_category_id` (`category_id`) USING BTREE,
  KEY `idx_status` (`art_status`) USING BTREE,
  KEY `idx_is_top` (`is_top`) USING BTREE,
  KEY `idx_published_at` (`published_at`) USING BTREE,
  KEY `idx_created_at` (`created_at`) USING BTREE,
  KEY `idx_deleted_at` (`deleted_at`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733863034423263 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='文章主表';

-- ----------------------------
-- Records of articles
-- ----------------------------
BEGIN;
INSERT INTO `articles` (`id`, `title`, `subtitle`, `art_cover`, `art_content`, `content_type`, `summary`, `category_id`, `tag_ids`, `author_id`, `author_name`, `source`, `source_url`, `art_status`, `is_top`, `is_original`, `is_commentable`, `seo_title`, `seo_keywords`, `seo_description`, `extra_fields`, `view_count`, `like_count`, `collect_count`, `share_count`, `comment_count`, `published_at`, `created_at`, `updated_at`, `deleted_at`, `reviewer_id`, `reviewed_at`, `reject_reason`) VALUES (1, '企业官网全新改版上线，开启数字化新征程', '历时3个月精心打造，全新视觉体验与功能升级', '/uploads/articles/cover/website_redesign.jpg', '<h2>全新官网，全新出发</h2><p>经过3个月的精心设计与开发，企业官网全新改版正式上线！</p><p>本次改版以用户为中心，全新设计了信息架构和视觉风格，并优化了移动端体验，让您随时随地获取最新资讯。</p><p><strong>主要升级亮点：</strong></p><ul><li>全新的品牌视觉形象</li><li>更流畅的页面加载速度</li><li>完善的响应式设计，适配各类设备</li><li>新增在线客服与即时咨询功能</li></ul><p>未来，我们将持续通过官网与大家分享企业动态、行业趋势与技术干货，敬请期待！</p>', 1, '企业官网全新改版正式上线，本次升级历时3个月，带来全新的视觉体验、更流畅的交互和更完善的功能。', 1, '[1, 2]', 920733860755423001, 'admin', '原创', '', 4, 1, 1, 1, '企业官网全新改版上线 - 品牌官网', '官网改版,企业官网,品牌升级,数字化', '企业官网全新改版正式上线，全新视觉与功能升级，开启数字化新征程。', '{\"video_url\": \"https://www.example.com/video/redesign.mp4\"}', 2580, 156, 89, 45, 23, '2026-07-20 09:00:00', '2026-07-15 10:00:00', '2026-07-23 09:47:02', NULL, 920733860755423002, '2026-07-18 14:00:00', '');
INSERT INTO `articles` (`id`, `title`, `subtitle`, `art_cover`, `art_content`, `content_type`, `summary`, `category_id`, `tag_ids`, `author_id`, `author_name`, `source`, `source_url`, `art_status`, `is_top`, `is_original`, `is_commentable`, `seo_title`, `seo_keywords`, `seo_description`, `extra_fields`, `view_count`, `like_count`, `collect_count`, `share_count`, `comment_count`, `published_at`, `created_at`, `updated_at`, `deleted_at`, `reviewer_id`, `reviewed_at`, `reject_reason`) VALUES (2, '企业荣获2026年度“科技创新示范企业”称号', '以技术创新驱动企业发展，获行业高度认可', '/uploads/articles/cover/tech_award.jpg', '<p>近日，在2026年度科技创新大会上，本公司凭借在数字化领域的技术创新和产业应用，荣获“科技创新示范企业”称号。</p><p>本次评选由行业协会联合多家权威机构共同举办，旨在表彰在技术创新、成果转化和产业带动方面表现突出的企业。经过严格评审，本公司在技术研发投入、专利数量、行业影响力等维度均位列前茅。</p><p>公司CTO表示：“技术创新是公司发展的核心驱动力。未来我们将继续加大研发投入，推动产业数字化升级。” </p>', 1, '公司荣获2026年度“科技创新示范企业”称号，充分体现了行业对公司技术创新能力的认可。', 1, '[3]', 920733860755423002, 'super_admin', '原创', '', 4, 0, 1, 1, '科技创新示范企业 - 企业荣誉', '科技创新,示范企业,企业荣誉,技术奖项', '公司荣获2026年度“科技创新示范企业”称号，彰显技术创新实力。', NULL, 1860, 89, 56, 34, 12, '2026-07-10 10:30:00', '2026-07-08 16:00:00', '2026-07-23 09:47:02', NULL, 920733860755423002, '2026-07-09 09:00:00', '');
INSERT INTO `articles` (`id`, `title`, `subtitle`, `art_cover`, `art_content`, `content_type`, `summary`, `category_id`, `tag_ids`, `author_id`, `author_name`, `source`, `source_url`, `art_status`, `is_top`, `is_original`, `is_commentable`, `seo_title`, `seo_keywords`, `seo_description`, `extra_fields`, `view_count`, `like_count`, `collect_count`, `share_count`, `comment_count`, `published_at`, `created_at`, `updated_at`, `deleted_at`, `reviewer_id`, `reviewed_at`, `reject_reason`) VALUES (3, '2026年企业数字化发展趋势报告', '深度解读数字化转型的新机遇与新挑战', '/uploads/articles/cover/digital_trend.jpg', '<p>2026年，企业数字化已从“可选项”变为“必选项”。根据最新行业报告，超过80%的企业将数字化转型作为核心战略。</p><p><strong>主要趋势：</strong></p><ul><li><strong>AI 驱动：</strong>人工智能正在重塑企业运营模式，从自动化到智能化</li><li><strong>数据资产：</strong>数据已成为企业最重要的资产之一</li><li><strong>云原生：</strong>云原生技术正在加速企业应用现代化</li><li><strong>安全合规：</strong>数据安全与合规成为企业数字化的重要基石</li></ul><p>报告建议，企业应从战略、组织、技术、文化四个维度全面推进数字化转型。</p>', 1, '深度解读2026年企业数字化发展趋势，从AI驱动、数据资产、云原生到安全合规，为企业提供转型参考。', 2, '[4, 5]', 920733860755423003, 'editor_zhang', '原创', '', 4, 1, 1, 1, '2026年企业数字化发展趋势报告', '企业数字化,数字化转型,AI,云原生,数据资产', '解读2026年企业数字化发展趋势，为企业转型提供参考和指引。', NULL, 3210, 245, 123, 78, 45, '2026-07-05 08:00:00', '2026-07-02 14:00:00', '2026-07-23 09:47:02', NULL, 920733860755423002, '2026-07-03 10:00:00', '');
INSERT INTO `articles` (`id`, `title`, `subtitle`, `art_cover`, `art_content`, `content_type`, `summary`, `category_id`, `tag_ids`, `author_id`, `author_name`, `source`, `source_url`, `art_status`, `is_top`, `is_original`, `is_commentable`, `seo_title`, `seo_keywords`, `seo_description`, `extra_fields`, `view_count`, `like_count`, `collect_count`, `share_count`, `comment_count`, `published_at`, `created_at`, `updated_at`, `deleted_at`, `reviewer_id`, `reviewed_at`, `reject_reason`) VALUES (4, '产品 3.0 版本正式发布，带来全新功能体验', 'AI智能推荐、多端同步、数据可视化三大核心升级', '/uploads/articles/cover/product_3.0.jpg', '<p>经过半年的研发迭代，产品 3.0 版本正式与大家见面了！</p><h3>三大核心升级：</h3><ul><li><strong>AI 智能推荐：</strong>基于用户行为数据，提供个性化的内容推荐</li><li><strong>多端同步：</strong>Web、App、小程序数据实时同步，无缝切换</li><li><strong>数据可视化：</strong>内置多维度数据分析看板，辅助决策</li></ul><p>本次更新还优化了超过50项用户体验细节，期待为您带来更高效的使用体验。即日起，所有用户可在线升级至最新版本。</p>', 1, '产品3.0版本正式发布，带来AI智能推荐、多端同步、数据可视化三大核心升级。', 3, '[6, 7]', 920733860755423004, 'ops_li', '原创', '', 4, 0, 1, 1, '产品3.0正式发布 - 新品发布', '产品发布,3.0版本,AI推荐,数据可视化', '产品3.0版本正式发布，三大核心升级助力企业高效运营。', '{\"version\": \"3.0.0\", \"download_url\": \"https://www.example.com/download/product-3.0\"}', 4560, 321, 156, 89, 56, '2026-07-18 10:00:00', '2026-07-16 09:00:00', '2026-07-23 09:47:02', NULL, 920733860755423002, '2026-07-17 11:00:00', '');
INSERT INTO `articles` (`id`, `title`, `subtitle`, `art_cover`, `art_content`, `content_type`, `summary`, `category_id`, `tag_ids`, `author_id`, `author_name`, `source`, `source_url`, `art_status`, `is_top`, `is_original`, `is_commentable`, `seo_title`, `seo_keywords`, `seo_description`, `extra_fields`, `view_count`, `like_count`, `collect_count`, `share_count`, `comment_count`, `published_at`, `created_at`, `updated_at`, `deleted_at`, `reviewer_id`, `reviewed_at`, `reject_reason`) VALUES (5, 'Hyperf 框架高性能实践：从入门到精通', '深入解析 Hyperf 的协程、依赖注入和注解机制', '/uploads/articles/cover/hyperf_best_practice.jpg', '<p>Hyperf 是 Swoole 生态中最流行的 PHP 框架之一，以其高性能和丰富的生态受到开发者的广泛关注。</p><h3>本篇主要分享：</h3><ul><li><strong>协程原理：</strong>理解 Swoole 协程的工作机制，写出高性能代码</li><li><strong>依赖注入：</strong>利用 DI 容器实现松耦合设计</li><li><strong>注解路由：</strong>使用注解简化路由配置，提高开发效率</li><li><strong>性能优化：</strong>常见的性能瓶颈分析与优化策略</li></ul><p>通过实际项目案例，帮助大家快速上手并写出高质量的 Hyperf 应用。</p>', 1, '深入解析 Hyperf 框架的核心机制，包括协程、依赖注入、注解路由和性能优化实践。', 4, '[8, 9]', 920733860755423001, 'admin', '原创', '', 4, 0, 1, 1, 'Hyperf框架高性能实践 - 技术分享', 'Hyperf,PHP,协程,依赖注入,性能优化', '深入解析 Hyperf 框架的核心机制与高性能实践。', NULL, 1980, 134, 67, 45, 28, '2026-07-12 14:30:00', '2026-07-10 11:00:00', '2026-07-23 09:47:02', NULL, 920733860755423002, '2026-07-11 09:00:00', '');
INSERT INTO `articles` (`id`, `title`, `subtitle`, `art_cover`, `art_content`, `content_type`, `summary`, `category_id`, `tag_ids`, `author_id`, `author_name`, `source`, `source_url`, `art_status`, `is_top`, `is_original`, `is_commentable`, `seo_title`, `seo_keywords`, `seo_description`, `extra_fields`, `view_count`, `like_count`, `collect_count`, `share_count`, `comment_count`, `published_at`, `created_at`, `updated_at`, `deleted_at`, `reviewer_id`, `reviewed_at`, `reject_reason`) VALUES (6, '某大型制造企业数字化转型成功案例', '借助企业数字化平台，实现生产效率提升30%', '/uploads/articles/cover/case_manufacturing.jpg', '<p>某大型制造企业拥有超过5000名员工，业务涵盖产品研发、生产制造、供应链管理等多个环节。</p><p><strong>业务痛点：</strong></p><ul><li>数据孤岛严重，各部门信息不互通</li><li>业务流程效率低，审批周期长</li><li>缺乏数据驱动的决策支持</li></ul><p><strong>解决方案：</strong></p><p>通过企业数字化平台，实现了统一的数据中台、自动化流程引擎和智能分析看板。</p><p><strong>效果：</strong></p><ul><li>生产效率提升 30%</li><li>审批周期缩短 50%</li><li>数据决策效率提升 40%</li></ul>', 1, '某大型制造企业通过企业数字化平台实现生产效率提升30%的数字化转型案例。', 6, '[10]', 920733860755423003, 'editor_zhang', '原创', '', 4, 0, 1, 1, '制造企业数字化转型成功案例', '数字化转型,制造企业,客户案例,效率提升', '某大型制造企业数字化转型成功案例，生产效率提升30%。', NULL, 1560, 98, 45, 23, 15, '2026-07-08 16:00:00', '2026-07-06 15:00:00', '2026-07-23 09:47:02', NULL, 920733860755423002, '2026-07-07 10:00:00', '');
INSERT INTO `articles` (`id`, `title`, `subtitle`, `art_cover`, `art_content`, `content_type`, `summary`, `category_id`, `tag_ids`, `author_id`, `author_name`, `source`, `source_url`, `art_status`, `is_top`, `is_original`, `is_commentable`, `seo_title`, `seo_keywords`, `seo_description`, `extra_fields`, `view_count`, `like_count`, `collect_count`, `share_count`, `comment_count`, `published_at`, `created_at`, `updated_at`, `deleted_at`, `reviewer_id`, `reviewed_at`, `reject_reason`) VALUES (7, '2026年第三季度产品路线图预告', '即将发布的新功能与改进计划', '/uploads/articles/cover/roadmap_q3.jpg', '<p>本文是2026年Q3产品路线图的草稿版本，正式发布前需要内部审核。</p><p><strong>计划中的主要功能：</strong></p><ul><li>AI 辅助写作功能</li><li>多语言国际化支持</li><li>数据导出增强</li><li>性能优化与稳定性提升</li></ul>', 1, '2026年Q3产品路线图预告，涵盖AI辅助写作、多语言支持等主要功能。', 3, '[11]', 920733860755423004, 'ops_li', '原创', '', 1, 0, 1, 1, '', '', '', NULL, 0, 0, 0, 0, 0, NULL, '2026-07-21 09:00:00', '2026-07-23 09:47:02', NULL, 0, NULL, '');
INSERT INTO `articles` (`id`, `title`, `subtitle`, `art_cover`, `art_content`, `content_type`, `summary`, `category_id`, `tag_ids`, `author_id`, `author_name`, `source`, `source_url`, `art_status`, `is_top`, `is_original`, `is_commentable`, `seo_title`, `seo_keywords`, `seo_description`, `extra_fields`, `view_count`, `like_count`, `collect_count`, `share_count`, `comment_count`, `published_at`, `created_at`, `updated_at`, `deleted_at`, `reviewer_id`, `reviewed_at`, `reject_reason`) VALUES (8, '企业社会责任报告：2026年度可持续发展', '践行ESG理念，推动可持续发展', '/uploads/articles/cover/csr_2026.jpg', '<p>2026年度企业社会责任报告正式提交审核，涵盖环境保护、员工关怀、社会公益等方面的工作成果。</p><p><strong>主要成果：</strong></p><ul><li>碳排放减少 15%</li><li>员工满意度达 92%</li><li>累计公益投入 500 万元</li></ul>', 1, '2026年度企业社会责任报告，涵盖环境保护、员工关怀、社会公益等方面。', 1, '[12]', 920733860755423005, 'pm_wang', '原创', '', 2, 0, 1, 1, '企业社会责任报告2026 - 可持续发展', '社会责任,ESG,可持续发展,公益', '2026年度企业社会责任报告，践行ESG理念，推动可持续发展。', NULL, 0, 0, 0, 0, 0, NULL, '2026-07-22 11:00:00', '2026-07-23 09:47:02', NULL, 0, NULL, '');
INSERT INTO `articles` (`id`, `title`, `subtitle`, `art_cover`, `art_content`, `content_type`, `summary`, `category_id`, `tag_ids`, `author_id`, `author_name`, `source`, `source_url`, `art_status`, `is_top`, `is_original`, `is_commentable`, `seo_title`, `seo_keywords`, `seo_description`, `extra_fields`, `view_count`, `like_count`, `collect_count`, `share_count`, `comment_count`, `published_at`, `created_at`, `updated_at`, `deleted_at`, `reviewer_id`, `reviewed_at`, `reject_reason`) VALUES (9, '2025年企业年度总结：砥砺前行，再创佳绩', '回顾2025，展望2026', '/uploads/articles/cover/yearly_2025.jpg', '<p>2025年是公司快速发展的一年，我们取得了以下成绩：</p><ul><li>营收同比增长 35%</li><li>服务客户突破 10,000 家</li><li>团队规模扩展至 500 人</li></ul><p>展望2026，我们将继续深耕行业，为客户创造更大的价值。</p>', 1, '2025年企业年度总结，回顾年度成绩与里程碑。', 1, '[13]', 920733860755423001, 'admin', '原创', '', 5, 0, 1, 1, '企业年度总结2025', '年度总结,企业成就,发展回顾', '2025年企业年度总结，回顾年度成绩与发展历程。', NULL, 8900, 567, 234, 123, 67, '2026-01-01 00:00:00', '2025-12-20 10:00:00', '2026-07-23 09:47:02', NULL, 920733860755423002, '2025-12-25 14:00:00', '');
INSERT INTO `articles` (`id`, `title`, `subtitle`, `art_cover`, `art_content`, `content_type`, `summary`, `category_id`, `tag_ids`, `author_id`, `author_name`, `source`, `source_url`, `art_status`, `is_top`, `is_original`, `is_commentable`, `seo_title`, `seo_keywords`, `seo_description`, `extra_fields`, `view_count`, `like_count`, `collect_count`, `share_count`, `comment_count`, `published_at`, `created_at`, `updated_at`, `deleted_at`, `reviewer_id`, `reviewed_at`, `reject_reason`) VALUES (10, '2026年行业峰会精彩回顾', '全球行业领袖共话未来', '/uploads/articles/cover/summit_2026.jpg', '<p>本次行业峰会邀请了全球超过50位行业领袖，共同探讨行业发展的机遇与挑战。</p><p>但由于提交的素材涉及部分未经授权的内容，本次审核被驳回。</p>', 1, '2026年行业峰会精彩回顾，全球行业领袖共话未来。', 2, '[14]', 920733860755423006, 'sales_chen', '转载', 'https://www.example.com/source/summit', 6, 0, 0, 1, '', '', '', NULL, 0, 0, 0, 0, 0, NULL, '2026-07-19 14:00:00', '2026-07-23 09:47:02', NULL, 920733860755423002, '2026-07-20 09:00:00', '文章中的图片素材涉及版权问题，请替换后重新提交审核。');
INSERT INTO `articles` (`id`, `title`, `subtitle`, `art_cover`, `art_content`, `content_type`, `summary`, `category_id`, `tag_ids`, `author_id`, `author_name`, `source`, `source_url`, `art_status`, `is_top`, `is_original`, `is_commentable`, `seo_title`, `seo_keywords`, `seo_description`, `extra_fields`, `view_count`, `like_count`, `collect_count`, `share_count`, `comment_count`, `published_at`, `created_at`, `updated_at`, `deleted_at`, `reviewer_id`, `reviewed_at`, `reject_reason`) VALUES (920733863034423262, '草稿测试', '', '', '<p>hello</p>', 1, 'hello', 920733863034423253, NULL, 934035802554576897, '管理员', '', '', 4, 0, 1, 1, '', '', '', NULL, 0, 0, 0, 0, 0, '2026-07-26 02:16:53', '2026-07-26 02:16:53', '2026-07-26 02:16:53', NULL, 934035802554576897, '2026-07-26 02:16:53', NULL);
COMMIT;

-- ----------------------------
-- Table structure for auth_menus
-- ----------------------------
DROP TABLE IF EXISTS `auth_menus`;
CREATE TABLE `auth_menus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `parent_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '父级菜单ID，0表示顶级菜单',
  `menu_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单名称，如：用户管理',
  `menu_icon` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单图标，如：el-icon-user',
  `menu_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '前端路由路径，如：/user/list',
  `component` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '前端组件路径，如：user/Index',
  `permission_code` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关联的权限标识，用于按钮级控制',
  `menu_sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序权重，值越大越靠前',
  `menu_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0=禁用，1=启用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间（软删除）',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_parent_id` (`parent_id`) USING BTREE,
  KEY `idx_permission_code` (`permission_code`) USING BTREE,
  KEY `idx_status` (`menu_status`) USING BTREE,
  KEY `idx_deleted_at` (`deleted_at`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733863034403284 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='菜单/功能表';

-- ----------------------------
-- Records of auth_menus
-- ----------------------------
BEGIN;
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423248, 0, '首页', 'Dashboard', '/backend/dashboard', '/backend/dashboard', 'dashboardview', 1000, 1, '2026-07-23 09:58:06', '2026-07-26 08:57:03', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423255, 0, '运营管理', 'Promotion', '', '', 'operationview', 50, 1, '2026-07-23 09:58:06', '2026-07-26 02:30:19', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423256, 920733860755423255, '广告位管理', '', '/backend/ad-slots', 'operation/AdSlotIndex', 'adslotsview', 50, 1, '2026-07-23 09:58:06', '2026-07-26 02:36:57', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423257, 920733860755423255, '广告管理', '', '/backend/ad-positions', 'operation/AdPositionIndex', 'adpositionsview', 40, 1, '2026-07-23 09:58:06', '2026-07-26 02:36:57', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423258, 920733860755423255, '友情链接', '', '/backend/friend-links', 'operation/FriendLinkIndex', 'friendlinksview', 30, 1, '2026-07-23 09:58:06', '2026-07-26 02:33:05', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423259, 920733860755423255, '用户留言', '', '/backend/feedbacks', 'operation/FeedbackIndex', 'feedbacksview', 20, 1, '2026-07-23 09:58:06', '2026-07-26 02:30:19', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423260, 920733860755423255, '招聘职位', '', '/backend/boss-jobs', 'operation/BossJobIndex', 'bossjobview', 10, 1, '2026-07-23 09:58:06', '2026-07-26 02:30:19', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423261, 0, '系统管理', 'Setting', '', '', 'systemview', 40, 1, '2026-07-23 09:58:06', '2026-07-26 02:33:05', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423262, 920733860755423261, '网站设置', '', '/backend/system/settings', 'system/SiteConfig', 'configview', 20, 1, '2026-07-23 09:58:06', '2026-07-26 02:33:05', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423263, 920733863034403263, '菜单管理', '', '/backend/menus', 'menus/index', 'menu.view', 10, 1, '2026-07-23 09:58:06', '2026-07-26 08:49:37', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423270, 920733863034403263, '用户管理', 'el-icon-user', '/backend/users', 'user/index', 'user.view', 4, 1, '2026-07-23 09:58:06', '2026-07-26 09:06:02', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423271, 920733860755423261, '操作日志', 'Document', '/backend/system/operation-logs', 'system/OperationLog', 'logview', 5, 1, '2026-07-23 09:58:06', '2026-07-26 02:04:52', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423272, 0, '书签管理', 'Star', '', '', 'bookmarkview', 55, 1, '2026-07-23 09:58:06', '2026-07-26 02:22:29', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423273, 920733860755423272, '书签列表', '', '/backend/bookmarks', 'bookmark/Index', 'bookmarklist', 10, 1, '2026-07-23 09:58:06', '2026-07-26 02:22:29', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403263, 0, '权限菜单分类管理', 'Lock', '', '', 'permission', 90, 1, '2026-07-24 11:54:43', '2026-07-24 11:54:43', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403264, 920733863034403263, '角色管理', '', '/backend/roles', 'roles/Index', 'role.view', 15, 1, '2026-07-24 11:54:43', '2026-07-24 11:54:43', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403265, 920733863034403263, '权限管理', '', '/backend/permissions', 'permissions/Index', 'permission.view', 25, 1, '2026-07-24 12:00:54', '2026-07-24 12:00:54', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403266, 0, '人事管理', 'Avatar', '', '', 'hr', 65, 1, '2026-07-25 10:03:56', '2026-07-25 10:03:56', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403267, 920733863034403266, '部门管理', '', '/backend/hr/departments', 'hr/Department', 'hr.department', 30, 1, '2026-07-25 10:03:56', '2026-07-25 10:03:56', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403268, 920733863034403266, '岗位管理', '', '/backend/hr/posts', 'hr/Post', 'hr.post', 20, 1, '2026-07-25 10:03:56', '2026-07-25 10:03:56', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403269, 920733863034403266, '任职管理', '', '/backend/hr/user-dept-posts', 'hr/UserDeptPost', 'hr.user_dept_post', 10, 1, '2026-07-25 10:03:56', '2026-07-25 10:03:56', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403270, 0, '商品管理', 'Goods', '', '', 'products', 40, 1, '2026-07-25 10:29:43', '2026-07-25 10:30:49', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403271, 920733863034403270, '品牌管理', '', '/backend/product/brands', 'product/Brand', 'product.brand', 40, 1, '2026-07-25 10:29:43', '2026-07-25 10:29:43', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403272, 920733863034403270, '商品分类', '', '/backend/product/categories', 'product/Category', 'product.category', 30, 1, '2026-07-25 10:29:43', '2026-07-25 10:29:43', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403273, 920733863034403270, '规格管理', '', '/backend/product/specifications', 'product/Specification', 'product.spec', 20, 1, '2026-07-25 10:29:43', '2026-07-25 10:29:43', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403274, 920733863034403270, '商品管理', '', '/backend/product/products', 'product/Index', 'product.product', 50, 1, '2026-07-25 10:37:40', '2026-07-25 10:37:40', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403275, 0, '流程管理', 'Share', '', '', 'wf', 35, 1, '2026-07-25 11:18:13', '2026-07-25 11:18:13', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403276, 920733863034403275, '流程类型', '', '/backend/wf/flow-types', 'wf/FlowType', 'wf.flow_type', 20, 1, '2026-07-25 11:18:13', '2026-07-25 11:18:13', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403277, 920733863034403275, '流程模板', '', '/backend/wf/flow-definitions', 'wf/FlowDefinitionIndex', 'wf.flow_definition', 10, 1, '2026-07-25 11:18:13', '2026-07-25 11:18:13', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403278, 920733863034403275, '待我审批', '', '/backend/wf/todo', 'wf/TodoIndex', 'wf.todo', 50, 1, '2026-07-25 11:25:17', '2026-07-25 11:25:17', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403279, 920733863034403275, '我的申请', '', '/backend/wf/applies', 'wf/ApplyIndex', 'wf.apply', 40, 1, '2026-07-25 11:25:17', '2026-07-25 11:25:17', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403280, 920733863034403275, '抄送我的', '', '/backend/wf/cc', 'wf/CcIndex', 'wf.cc', 30, 1, '2026-07-25 11:25:17', '2026-07-25 11:25:17', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403281, 0, '内容管理', 'Document', '', '', 'news', 60, 1, '2026-07-26 02:16:51', '2026-07-26 10:18:55', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403282, 920733863034403281, '文章管理', '', '/backend/news/articles', 'news/ArticleIndex', 'news.article', 20, 1, '2026-07-26 02:16:51', '2026-07-26 02:16:51', NULL);
INSERT INTO `auth_menus` (`id`, `parent_id`, `menu_name`, `menu_icon`, `menu_path`, `component`, `permission_code`, `menu_sort`, `menu_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863034403283, 920733863034403281, '分类管理', '', '/backend/news/categories', 'news/Category', 'news.category', 10, 1, '2026-07-26 02:16:51', '2026-07-26 02:16:51', NULL);
COMMIT;

-- ----------------------------
-- Table structure for auth_permissions
-- ----------------------------
DROP TABLE IF EXISTS `auth_permissions`;
CREATE TABLE `auth_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `parent_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '父级权限ID，用于树形结构',
  `per_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '权限名称，如：用户删除',
  `per_code` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '权限唯一标识，如：user:delete',
  `per_type` enum('menu','button','api') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'api' COMMENT '权限类型：menu=菜单，button=按钮，api=接口',
  `per_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT '前端路由路径或API路径，如：/user/delete',
  `per_method` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT 'HTTP方法，GET/POST/PUT/DELETE，仅 type=api 时有效',
  `per_icon` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT '菜单图标，仅 type=menu 时有效',
  `per_sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序权重，值越大越靠前',
  `per_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0=禁用，1=启用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间（软删除）',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_code` (`per_code`) USING BTREE,
  KEY `idx_parent_id` (`parent_id`) USING BTREE,
  KEY `idx_type` (`per_type`) USING BTREE,
  KEY `idx_status` (`per_status`) USING BTREE,
  KEY `idx_deleted_at` (`deleted_at`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733862755423293 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='权限规则表';

-- ----------------------------
-- Records of auth_permissions
-- ----------------------------
BEGIN;
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423246, 0, '首页仪表盘', 'dashboard:view', 'menu', '/dashboard', '', 'el-icon-s-home', 100, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423247, 0, '内容管理', 'content:view', 'menu', '/content', '', 'el-icon-document-copy', 90, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423248, 0, '运营管理', 'operation:view', 'menu', '/operation', '', 'el-icon-present', 80, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423249, 0, '系统管理', 'system:view', 'menu', '/system', '', 'el-icon-setting', 70, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423250, 0, '书签管理', 'bookmark:view', 'menu', '/bookmark', '', 'el-icon-star-off', 60, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423251, 920733862755423247, '文章管理', 'article:view', 'menu', '/content/article', '', 'el-icon-edit-outline', 10, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423252, 920733862755423247, '分类管理', 'category:view', 'menu', '/content/category', '', 'el-icon-folder-opened', 9, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423253, 920733862755423247, '友情链接', 'friendlink:view', 'menu', '/content/friendlink', '', 'el-icon-link', 8, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423254, 920733862755423248, '横幅管理', 'banner:view', 'menu', '/operation/banner', '', 'el-icon-picture', 20, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423255, 920733862755423248, '广告位管理', 'ad:view', 'menu', '/operation/ad', '', 'el-icon-office-building', 19, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423256, 920733862755423248, '留言管理', 'feedback:view', 'menu', '/operation/feedback', '', 'el-icon-chat-line-round', 18, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423257, 920733862755423249, '系统设置', 'config:view', 'menu', '/system/config', '', 'el-icon-tools', 30, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423258, 920733862755423249, '菜单管理', 'menu:view', 'menu', '/system/menu', '', 'el-icon-menu', 29, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423259, 920733862755423249, '权限管理', 'permission:view', 'menu', '/system/permission', '', 'el-icon-lock', 28, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423260, 920733862755423249, '用户管理', 'user:view', 'menu', '/system/user', '', 'el-icon-user', 27, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423261, 920733862755423249, '操作日志', 'log:view', 'menu', '/system/log', '', 'el-icon-document', 26, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423262, 920733862755423250, '书签列表', 'bookmark:list', 'menu', '/bookmark/list', '', 'el-icon-star-on', 10, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423263, 920733862755423250, '我的书签', 'bookmark:my', 'menu', '/bookmark/my', '', 'el-icon-collection', 9, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423264, 920733862755423251, '文章列表', 'article:list', 'button', '/content/article/list', '', '', 10, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423265, 920733862755423251, '添加文章', 'article:add', 'button', '/content/article/add', '', '', 9, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423266, 920733862755423251, '编辑文章', 'article:edit', 'button', '/content/article/edit', '', '', 8, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423267, 920733862755423251, '删除文章', 'article:delete', 'button', '/content/article/delete', '', '', 7, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423268, 920733862755423252, '添加分类', 'category:add', 'button', '/content/category/add', '', '', 5, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423269, 920733862755423252, '编辑分类', 'category:edit', 'button', '/content/category/edit', '', '', 4, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423270, 920733862755423252, '删除分类', 'category:delete', 'button', '/content/category/delete', '', '', 3, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423271, 920733862755423253, '添加友情链接', 'friendlink:add', 'button', '/content/friendlink/add', '', '', 5, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423272, 920733862755423253, '编辑友情链接', 'friendlink:edit', 'button', '/content/friendlink/edit', '', '', 4, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423273, 920733862755423253, '删除友情链接', 'friendlink:delete', 'button', '/content/friendlink/delete', '', '', 3, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423274, 920733862755423254, '添加横幅', 'banner:add', 'button', '/operation/banner/add', '', '', 5, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423275, 920733862755423254, '编辑横幅', 'banner:edit', 'button', '/operation/banner/edit', '', '', 4, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423276, 920733862755423254, '删除横幅', 'banner:delete', 'button', '/operation/banner/delete', '', '', 3, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423277, 920733862755423255, '添加广告位', 'ad:add', 'button', '/operation/ad/add', '', '', 5, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423278, 920733862755423255, '编辑广告位', 'ad:edit', 'button', '/operation/ad/edit', '', '', 4, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423279, 920733862755423255, '删除广告位', 'ad:delete', 'button', '/operation/ad/delete', '', '', 3, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423280, 920733862755423256, '处理留言', 'feedback:handle', 'button', '/operation/feedback/handle', '', '', 5, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423281, 920733862755423256, '删除留言', 'feedback:delete', 'button', '/operation/feedback/delete', '', '', 3, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423282, 0, '角色管理', 'role:view', 'menu', '/system/permission/role', '', '', 10, 1, '2026-07-23 08:04:21', '2026-07-23 08:04:21', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423283, 920733862755423260, '添加用户', 'user:add', 'button', '/system/user/add', '', '', 10, 1, '2026-07-23 08:06:48', '2026-07-23 08:06:48', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423284, 920733862755423283, '添加用户API', 'api:user:add', 'api', '/api/user/add', 'POST', '', 5, 1, '2026-07-23 08:06:48', '2026-07-23 08:06:48', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423285, 920733862755423260, '编辑用户', 'user:edit', 'button', '/system/user/edit', '', '', 9, 1, '2026-07-23 08:06:48', '2026-07-23 08:06:48', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423286, 920733862755423285, '编辑用户API', 'api:user:edit', 'api', '/api/user/edit', 'PUT', '', 4, 1, '2026-07-23 08:06:48', '2026-07-23 08:06:48', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423287, 920733862755423260, '删除用户', 'user:delete', 'button', '/system/user/delete', '', '', 8, 1, '2026-07-23 08:06:48', '2026-07-23 08:06:48', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423288, 920733862755423287, '删除用户API', 'api:user:delete', 'api', '/api/user/delete', 'DELETE', '', 3, 1, '2026-07-23 08:06:48', '2026-07-23 08:06:48', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423289, 920733862755423264, '文章列表API', 'api:article:list', 'api', '/api/article/list', 'GET', '', 10, 1, '2026-07-23 08:07:06', '2026-07-23 08:07:06', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423290, 920733862755423265, '添加文章API', 'api:article:add', 'api', '/api/article/add', 'POST', '', 9, 1, '2026-07-23 08:07:06', '2026-07-23 08:07:06', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423291, 920733862755423266, '编辑文章API', 'api:article:edit', 'api', '/api/article/edit', 'PUT', '', 8, 1, '2026-07-23 08:07:06', '2026-07-23 08:07:06', NULL);
INSERT INTO `auth_permissions` (`id`, `parent_id`, `per_name`, `per_code`, `per_type`, `per_path`, `per_method`, `per_icon`, `per_sort`, `per_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862755423292, 920733862755423267, '删除文章API', 'api:article:delete', 'api', '/api/article/delete', 'DELETE', '', 7, 1, '2026-07-23 08:07:06', '2026-07-23 08:07:06', NULL);
COMMIT;

-- ----------------------------
-- Table structure for auth_role
-- ----------------------------
DROP TABLE IF EXISTS `auth_role`;
CREATE TABLE `auth_role` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `role_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '角色名称 如 超级管理员',
  `role_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '角色唯一标识（代码鉴权使用，如 finance_admin）',
  `role_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '角色类型: 1=系统内置 2=用户自定义',
  `role_sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  `data_scope` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '数据权限范围 1全部数据 2本部门及下级 3本部门 4仅本人数据 5自定义指定部门',
  `scope_departments` json DEFAULT NULL COMMENT '指定部门IDs，JSON格式',
  `role_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '0禁用 1启用',
  `role_remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '角色备注',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_role_code` (`role_code`,`deleted_at`) USING BTREE,
  KEY `idx_status` (`role_status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733860755423258 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='角色信息表';

-- ----------------------------
-- Records of auth_role
-- ----------------------------
BEGIN;
INSERT INTO `auth_role` (`id`, `role_name`, `role_code`, `role_type`, `role_sort`, `data_scope`, `scope_departments`, `role_status`, `role_remark`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423247, '超级管理员', 'super_admin', 1, 100, 1, NULL, 1, '系统内置超级管理员，拥有全部数据权限', '2026-07-23 07:58:27', '2026-07-24 11:54:43', NULL);
INSERT INTO `auth_role` (`id`, `role_name`, `role_code`, `role_type`, `role_sort`, `data_scope`, `scope_departments`, `role_status`, `role_remark`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423248, '系统管理员', 'system_admin', 1, 90, 1, NULL, 1, '负责系统运维、基础配置、用户管理、菜单权限分配', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` (`id`, `role_name`, `role_code`, `role_type`, `role_sort`, `data_scope`, `scope_departments`, `role_status`, `role_remark`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423249, '内容管理员', 'content_admin', 1, 80, 2, NULL, 1, '负责内容管理（文章、分类、友情链接等），可管理本部门及下级内容', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` (`id`, `role_name`, `role_code`, `role_type`, `role_sort`, `data_scope`, `scope_departments`, `role_status`, `role_remark`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423250, '运营管理员', 'operation_admin', 1, 70, 2, NULL, 1, '负责运营管理（横幅、广告位、留言等），可管理本部门及下级数据', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` (`id`, `role_name`, `role_code`, `role_type`, `role_sort`, `data_scope`, `scope_departments`, `role_status`, `role_remark`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423251, '内容编辑', 'content_editor', 2, 60, 3, NULL, 1, '负责文章内容的编辑、发布、修改，仅可操作本部门数据', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` (`id`, `role_name`, `role_code`, `role_type`, `role_sort`, `data_scope`, `scope_departments`, `role_status`, `role_remark`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423252, '运营专员', 'operation_specialist', 2, 50, 3, NULL, 1, '负责横幅、广告位、留言等日常运营操作，仅可操作本部门数据', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` (`id`, `role_name`, `role_code`, `role_type`, `role_sort`, `data_scope`, `scope_departments`, `role_status`, `role_remark`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423253, '访客用户', 'guest_user', 2, 10, 4, NULL, 1, '仅可查看公开内容，无编辑权限，数据仅限本人相关', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` (`id`, `role_name`, `role_code`, `role_type`, `role_sort`, `data_scope`, `scope_departments`, `role_status`, `role_remark`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423254, '部门经理', 'dept_manager', 2, 40, 2, NULL, 1, '可管理本部门及下级部门的所有数据，包括内容审核、运营审批等', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` (`id`, `role_name`, `role_code`, `role_type`, `role_sort`, `data_scope`, `scope_departments`, `role_status`, `role_remark`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423255, '人事管理员', 'hr_admin', 2, 30, 2, NULL, 1, '负责招聘管理、用户管理、组织架构等，可管理本部门及下级数据', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` (`id`, `role_name`, `role_code`, `role_type`, `role_sort`, `data_scope`, `scope_departments`, `role_status`, `role_remark`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423256, '财务管理员', 'finance_admin', 2, 20, 1, NULL, 1, '负责财务相关数据查看与管理，拥有全部财务数据权限', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` (`id`, `role_name`, `role_code`, `role_type`, `role_sort`, `data_scope`, `scope_departments`, `role_status`, `role_remark`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423257, '管理员', 'admin', 1, 90, 1, NULL, 1, '系统内置管理员', '2026-07-24 11:54:43', '2026-07-24 11:54:43', NULL);
COMMIT;

-- ----------------------------
-- Table structure for auth_role_menus
-- ----------------------------
DROP TABLE IF EXISTS `auth_role_menus`;
CREATE TABLE `auth_role_menus` (
  `role_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '角色ID（关联 auth_roles.id）',
  `menu_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '菜单ID（关联 auth_menus.id）',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`role_id`,`menu_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='角色-菜单关联表';

-- ----------------------------
-- Records of auth_role_menus
-- ----------------------------
BEGIN;
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423248, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423249, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423250, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423251, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423252, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423253, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423254, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423255, '2026-07-26 02:36:57');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423256, '2026-07-26 02:36:57');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423257, '2026-07-26 02:36:57');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423258, '2026-07-26 02:36:57');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423259, '2026-07-26 02:36:57');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423260, '2026-07-26 02:36:57');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423261, '2026-07-26 02:36:57');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423262, '2026-07-26 02:36:57');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423263, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423264, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423265, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423266, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423267, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423268, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423269, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423270, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423271, '2026-07-26 02:04:52');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423272, '2026-07-26 02:22:29');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423273, '2026-07-26 02:22:29');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733860755423274, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403263, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403264, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403265, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403266, '2026-07-25 10:03:56');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403267, '2026-07-25 10:03:56');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403268, '2026-07-25 10:03:56');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403269, '2026-07-25 10:03:56');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403270, '2026-07-25 10:37:40');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403271, '2026-07-25 10:37:40');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403272, '2026-07-25 10:37:40');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403273, '2026-07-25 10:37:40');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403274, '2026-07-25 10:37:40');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403275, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403276, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403277, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403278, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403279, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403280, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403281, '2026-07-26 02:16:51');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403282, '2026-07-26 02:16:51');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423247, 920733863034403283, '2026-07-26 02:16:51');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423255, 920733863034403266, '2026-07-26 01:59:48');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423255, 920733863034403267, '2026-07-26 01:59:48');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423255, 920733863034403268, '2026-07-26 01:59:48');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423255, 920733863034403269, '2026-07-26 01:59:48');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733860755423248, '2026-07-26 01:45:13');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733860755423255, '2026-07-26 02:36:57');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733860755423256, '2026-07-26 02:36:57');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733860755423257, '2026-07-26 02:36:57');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733860755423258, '2026-07-26 02:36:57');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733860755423259, '2026-07-26 02:36:57');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733860755423260, '2026-07-26 02:36:57');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733860755423261, '2026-07-26 02:36:57');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733860755423262, '2026-07-26 02:36:57');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733860755423263, '2026-07-26 01:45:13');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733860755423270, '2026-07-26 01:45:13');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733860755423271, '2026-07-26 02:04:52');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733860755423272, '2026-07-26 02:22:29');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733860755423273, '2026-07-26 02:22:29');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733863034403263, '2026-07-26 01:45:13');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733863034403264, '2026-07-26 01:45:13');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733863034403265, '2026-07-26 01:45:13');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733863034403281, '2026-07-26 02:16:51');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733863034403282, '2026-07-26 02:16:51');
INSERT INTO `auth_role_menus` (`role_id`, `menu_id`, `created_at`) VALUES (920733860755423257, 920733863034403283, '2026-07-26 02:16:51');
COMMIT;

-- ----------------------------
-- Table structure for auth_role_permissions
-- ----------------------------
DROP TABLE IF EXISTS `auth_role_permissions`;
CREATE TABLE `auth_role_permissions` (
  `role_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '角色ID（关联 auth_roles.id）',
  `permission_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '权限ID（关联 auth_permissions.id）',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`role_id`,`permission_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='角色-权限关联表';

-- ----------------------------
-- Records of auth_role_permissions
-- ----------------------------
BEGIN;
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423246, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423247, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423248, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423249, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423250, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423251, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423252, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423253, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423254, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423255, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423256, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423257, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423258, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423259, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423260, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423261, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423262, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423263, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423264, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423265, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423266, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423267, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423268, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423269, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423270, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423271, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423272, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423273, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423274, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423275, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423276, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423277, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423278, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423279, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423280, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423281, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423282, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423283, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423284, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423285, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423286, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423287, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423288, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423289, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423290, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423291, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423247, 920733862755423292, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423248, 920733862755423246, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423248, 920733862755423249, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423248, 920733862755423257, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423248, 920733862755423258, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423248, 920733862755423259, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423248, 920733862755423260, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423248, 920733862755423261, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423248, 920733862755423282, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423248, 920733862755423283, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423248, 920733862755423284, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423248, 920733862755423285, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423248, 920733862755423286, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423248, 920733862755423287, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423248, 920733862755423288, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423247, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423251, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423252, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423253, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423264, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423265, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423266, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423267, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423268, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423269, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423270, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423271, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423272, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423273, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423289, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423290, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423291, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423249, 920733862755423292, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423250, 920733862755423248, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423250, 920733862755423254, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423250, 920733862755423255, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423250, 920733862755423256, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423250, 920733862755423274, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423250, 920733862755423275, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423250, 920733862755423276, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423250, 920733862755423277, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423250, 920733862755423278, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423250, 920733862755423279, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423250, 920733862755423280, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423250, 920733862755423281, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423251, 920733862755423251, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423251, 920733862755423264, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423251, 920733862755423265, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423251, 920733862755423266, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423251, 920733862755423267, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423252, 920733862755423254, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423252, 920733862755423255, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423252, 920733862755423256, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423252, 920733862755423274, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423252, 920733862755423275, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423252, 920733862755423277, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423252, 920733862755423278, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423252, 920733862755423280, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423253, 920733862755423246, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423253, 920733862755423264, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423247, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423248, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423251, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423252, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423253, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423254, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423255, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423256, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423264, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423265, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423266, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423267, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423268, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423269, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423270, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423271, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423272, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423273, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423274, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423275, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423276, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423277, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423278, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423279, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423280, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423254, 920733862755423281, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423255, 920733862755423249, '2026-07-26 01:59:48');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423255, 920733862755423260, '2026-07-26 01:59:48');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423255, 920733862755423283, '2026-07-26 01:59:48');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423255, 920733862755423284, '2026-07-26 01:59:48');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423255, 920733862755423285, '2026-07-26 01:59:48');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423255, 920733862755423286, '2026-07-26 01:59:48');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423255, 920733862755423287, '2026-07-26 01:59:48');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423255, 920733862755423288, '2026-07-26 01:59:48');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423256, 920733862755423246, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423246, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423247, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423248, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423249, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423250, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423251, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423252, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423253, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423254, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423255, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423256, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423257, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423258, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423259, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423260, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423261, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423262, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423263, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423264, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423265, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423266, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423267, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423268, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423269, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423270, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423271, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423272, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423273, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423274, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423275, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423276, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423277, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423278, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423279, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423280, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423281, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423282, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423283, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423284, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423285, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423286, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423287, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423288, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423289, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423290, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423291, '2026-07-26 01:45:13');
INSERT INTO `auth_role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES (920733860755423257, 920733862755423292, '2026-07-26 01:45:13');
COMMIT;

-- ----------------------------
-- Table structure for auth_user_role
-- ----------------------------
DROP TABLE IF EXISTS `auth_user_role`;
CREATE TABLE `auth_user_role` (
  `user_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `role_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '角色ID',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`user_id`,`role_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='用户-角色关联';

-- ----------------------------
-- Records of auth_user_role
-- ----------------------------
BEGIN;
INSERT INTO `auth_user_role` (`user_id`, `role_id`, `created_at`) VALUES (920733860755423002, 920733860755423247, '2026-07-23 09:50:36');
INSERT INTO `auth_user_role` (`user_id`, `role_id`, `created_at`) VALUES (920733860755423003, 920733860755423251, '2026-07-23 09:50:36');
INSERT INTO `auth_user_role` (`user_id`, `role_id`, `created_at`) VALUES (920733860755423004, 920733860755423252, '2026-07-23 09:50:36');
INSERT INTO `auth_user_role` (`user_id`, `role_id`, `created_at`) VALUES (920733860755423005, 920733860755423254, '2026-07-23 09:50:36');
INSERT INTO `auth_user_role` (`user_id`, `role_id`, `created_at`) VALUES (920733860755423006, 920733860755423253, '2026-07-23 09:50:36');
INSERT INTO `auth_user_role` (`user_id`, `role_id`, `created_at`) VALUES (920733860755423007, 920733860755423256, '2026-07-23 09:50:36');
INSERT INTO `auth_user_role` (`user_id`, `role_id`, `created_at`) VALUES (920733860755423008, 920733860755423253, '2026-07-23 09:50:36');
INSERT INTO `auth_user_role` (`user_id`, `role_id`, `created_at`) VALUES (934035802554576896, 920733860755423255, '2026-07-26 09:48:49');
INSERT INTO `auth_user_role` (`user_id`, `role_id`, `created_at`) VALUES (934035802554576897, 920733860755423247, '2026-07-24 12:06:36');
COMMIT;

-- ----------------------------
-- Table structure for book_mark
-- ----------------------------
DROP TABLE IF EXISTS `book_mark`;
CREATE TABLE `book_mark` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `category_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '所属分类ID，关联 category 表，0表示未分类/默认书签栏',
  `short_title` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '书签短标题',
  `book_title` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '书签长标题',
  `book_url` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '书签链接地址',
  `book_favicon` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '网站图标URL',
  `book_desc` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '书签描述/备注',
  `sort_order` int unsigned NOT NULL DEFAULT '0' COMMENT '排序权重，值越小越靠前',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0-隐藏，1-正常，2-失效',
  `is_bold` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '显示：0-加粗，1-正常',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `created_by` bigint unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_category_sort` (`category_id`,`sort_order`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=934041315296100357 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='书签表';

-- ----------------------------
-- Records of book_mark
-- ----------------------------
BEGIN;
INSERT INTO `book_mark` (`id`, `category_id`, `short_title`, `book_title`, `book_url`, `book_favicon`, `book_desc`, `sort_order`, `status`, `is_bold`, `created_at`, `created_by`, `updated_at`) VALUES (934041315296100352, 935126090643668998, '豆包', '字节-豆包', 'https://www.doubao.com/chat/', '', '', 0, 1, 1, '2026-07-22 02:33:48', 0, '2026-07-22 02:35:35');
INSERT INTO `book_mark` (`id`, `category_id`, `short_title`, `book_title`, `book_url`, `book_favicon`, `book_desc`, `sort_order`, `status`, `is_bold`, `created_at`, `created_by`, `updated_at`) VALUES (934041315296100353, 935126090643668998, 'deepseek', 'Deepseek', 'https://chat.deepseek.com/', '', '', 0, 1, 0, '2026-07-22 02:37:10', 0, '2026-07-22 02:37:10');
INSERT INTO `book_mark` (`id`, `category_id`, `short_title`, `book_title`, `book_url`, `book_favicon`, `book_desc`, `sort_order`, `status`, `is_bold`, `created_at`, `created_by`, `updated_at`) VALUES (934041315296100354, 935126090643668998, '千问', '阿里巴巴-千问', 'https://www.qianwen.com/chat/', '', '', 0, 1, 0, '2026-07-22 02:37:44', 0, '2026-07-22 02:38:13');
INSERT INTO `book_mark` (`id`, `category_id`, `short_title`, `book_title`, `book_url`, `book_favicon`, `book_desc`, `sort_order`, `status`, `is_bold`, `created_at`, `created_by`, `updated_at`) VALUES (934041315296100355, 935126090643668993, '11', '22', 'http://192.168.124.87:8080/backend/category', 'sdsdsds', '2026-07-23 19:12:52', 22, 0, 0, '2026-07-23 19:13:07', 934035802554576897, '2026-07-23 11:13:37');
COMMIT;

-- ----------------------------
-- Table structure for boss_job
-- ----------------------------
DROP TABLE IF EXISTS `boss_job`;
CREATE TABLE `boss_job` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_title` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '职位名称',
  `department` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '所属部门',
  `workplace` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '工作地点',
  `experience` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT '经验要求',
  `education` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT '学历要求',
  `salary_range` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT '薪资范围',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '职位描述',
  `requirements` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '任职要求',
  `benefits` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '福利待遇',
  `is_hot` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否急聘',
  `job_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '1=待发布，2=发布中，3=已关闭',
  `expire_at` datetime DEFAULT NULL COMMENT '过期时间',
  `view_count` int unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `job_sort` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733863004423260 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='招聘职位表';

-- ----------------------------
-- Records of boss_job
-- ----------------------------
BEGIN;
INSERT INTO `boss_job` (`id`, `job_title`, `department`, `workplace`, `experience`, `education`, `salary_range`, `description`, `requirements`, `benefits`, `is_hot`, `job_status`, `expire_at`, `view_count`, `job_sort`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863004423246, 'PHP高级开发工程师', '技术研发中心', '深圳南山区科技园', '3-5年', '本科', '25K-40K·14薪', '1. 负责公司核心业务系统的架构设计与开发；\n2. 参与技术方案评审，制定技术规范；\n3. 攻克技术难点，优化系统性能；\n4. 指导初中级开发工程师，带领团队完成项目交付。', '1. 本科及以上学历，计算机相关专业，3年以上PHP开发经验；\n2. 精通PHP 8 + Hyperf / ThinkPHP 等主流框架；\n3. 熟悉MySQL数据库设计、索引优化、SQL调优；\n4. 熟悉Redis、RabbitMQ等中间件，有高并发项目经验；\n5. 熟悉Linux环境，掌握Docker容器化部署；\n6. 具备良好的代码规范和团队协作能力。', '五险一金、补充医疗保险、年度体检、弹性工作、餐补交通补、年度旅游、技术培训基金、年终奖', 1, 2, '2026-12-31 23:59:59', 1680, 100, '2026-07-23 09:23:10', '2026-07-23 09:23:10', NULL);
INSERT INTO `boss_job` (`id`, `job_title`, `department`, `workplace`, `experience`, `education`, `salary_range`, `description`, `requirements`, `benefits`, `is_hot`, `job_status`, `expire_at`, `view_count`, `job_sort`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863004423247, '前端开发工程师', '技术研发中心', '深圳南山区科技园', '2-4年', '本科', '18K-30K·14薪', '1. 负责公司产品Web端、移动端页面开发与维护；\n2. 与产品、UI、后端团队紧密协作，高效完成功能迭代；\n3. 参与前端技术选型和架构优化；\n4. 关注前端前沿技术，推动团队技术升级。', '1. 本科及以上学历，2年以上前端开发经验；\n2. 精通HTML5、CSS3、JavaScript，熟悉ES6+语法；\n3. 熟悉Vue 3 / React 等主流框架，有实际项目经验；\n4. 熟悉Vite / Webpack等构建工具；\n5. 了解HTTP协议，具备跨端、响应式开发能力；\n6. 有TypeScript、小程序开发经验者优先。', '五险一金、弹性工作、年度体检、餐补交通补、技术培训基金、年终奖', 0, 2, '2026-10-31 23:59:59', 952, 90, '2026-07-23 09:23:10', '2026-07-23 09:23:10', NULL);
INSERT INTO `boss_job` (`id`, `job_title`, `department`, `workplace`, `experience`, `education`, `salary_range`, `description`, `requirements`, `benefits`, `is_hot`, `job_status`, `expire_at`, `view_count`, `job_sort`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863004423248, '数据库工程师（DBA）', '技术研发中心', '深圳南山区科技园', '3-5年', '本科', '22K-35K·14薪', '1. 负责公司MySQL数据库的日常运维、性能调优和高可用架构设计；\n2. 制定数据库备份、恢复策略，保障数据安全；\n3. 参与数据库架构设计评审，提供专业建议；\n4. 建立数据库监控体系，提前发现并解决性能瓶颈。', '1. 本科及以上学历，3年以上MySQL DBA经验；\n2. 精通MySQL体系结构，熟悉InnoDB存储引擎；\n3. 掌握主从复制、分库分表、读写分离等架构方案；\n4. 熟悉Linux运维，掌握Shell/Python脚本开发；\n5. 有TiDB、OceanBase等分布式数据库经验者优先。', '五险一金、年度体检、技术培训基金、年终奖、弹性工作', 0, 2, '2026-09-30 23:59:59', 486, 80, '2026-07-23 09:23:10', '2026-07-23 09:23:10', NULL);
INSERT INTO `boss_job` (`id`, `job_title`, `department`, `workplace`, `experience`, `education`, `salary_range`, `description`, `requirements`, `benefits`, `is_hot`, `job_status`, `expire_at`, `view_count`, `job_sort`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863004423249, '产品经理', '产品中心', '深圳南山区科技园', '3-5年', '本科', '20K-35K·14薪', '1. 负责公司核心产品的需求调研、产品规划和功能设计；\n2. 撰写产品需求文档（PRD），协调设计、研发、测试团队推进产品迭代；\n3. 跟踪产品上线数据，分析用户行为，持续优化产品体验；\n4. 关注行业动态和竞品分析，制定产品差异化策略。', '1. 本科及以上学历，3年以上产品经理经验；\n2. 具备优秀的逻辑思维和沟通协调能力；\n3. 熟练使用Axure、Figma、XMind等产品设计工具；\n4. 有ToB企业服务或SaaS产品经验者优先；\n5. 具备数据分析能力，能通过数据驱动产品决策。', '五险一金、弹性工作、年度体检、产品学习基金、年终奖', 0, 2, '2026-11-30 23:59:59', 723, 85, '2026-07-23 09:23:10', '2026-07-23 09:23:10', NULL);
INSERT INTO `boss_job` (`id`, `job_title`, `department`, `workplace`, `experience`, `education`, `salary_range`, `description`, `requirements`, `benefits`, `is_hot`, `job_status`, `expire_at`, `view_count`, `job_sort`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863004423250, 'UI/UX设计师', '产品中心', '深圳南山区科技园', '2-4年', '本科', '15K-25K·14薪', '1. 负责公司Web端、移动端产品的UI/UX设计工作；\n2. 参与产品需求评审，从设计角度提出用户体验优化方案；\n3. 建立和维护设计规范体系，保证产品视觉一致性；\n4. 跟踪设计落地效果，持续优化产品交互体验。', '1. 本科及以上学历，设计相关专业，2年以上UI/UX设计经验；\n2. 熟练使用Figma、Sketch、Photoshop、Illustrator等设计工具；\n3. 具备良好的视觉设计能力和交互设计思维；\n4. 熟悉Web/iOS/Android等平台设计规范；\n5. 有ToB产品设计经验者优先。', '五险一金、弹性工作、年度体检、设计培训基金、年终奖', 0, 2, '2026-10-15 23:59:59', 634, 80, '2026-07-23 09:23:10', '2026-07-23 09:23:10', NULL);
INSERT INTO `boss_job` (`id`, `job_title`, `department`, `workplace`, `experience`, `education`, `salary_range`, `description`, `requirements`, `benefits`, `is_hot`, `job_status`, `expire_at`, `view_count`, `job_sort`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863004423251, '内容运营经理', '市场运营中心', '深圳南山区科技园', '2-4年', '本科', '15K-25K·14薪', '1. 负责公司网站、公众号、视频号等平台的内容规划和运营；\n2. 策划和撰写高质量的行业文章、产品软文和品牌内容；\n3. 制定内容传播策略，提升品牌影响力和用户活跃度；\n4. 跟踪内容数据，不断优化内容方向和运营策略。', '1. 本科及以上学历，2年以上内容运营经验；\n2. 具备优秀的文字功底和内容策划能力；\n3. 熟悉微信公众号、视频号、知乎等主流内容平台玩法；\n4. 有SEO内容运营经验者优先；\n5. 具备数据分析能力，能通过数据优化运营策略。', '五险一金、弹性工作、年度体检、年度旅游、年终奖', 0, 2, '2026-09-30 23:59:59', 458, 70, '2026-07-23 09:23:10', '2026-07-23 09:23:10', NULL);
INSERT INTO `boss_job` (`id`, `job_title`, `department`, `workplace`, `experience`, `education`, `salary_range`, `description`, `requirements`, `benefits`, `is_hot`, `job_status`, `expire_at`, `view_count`, `job_sort`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863004423252, '新媒体运营专员', '市场运营中心', '深圳南山区科技园', '1-3年', '大专', '8K-15K·13薪', '1. 负责公司微信、微博、抖音、小红书等新媒体平台日常运营；\n2. 策划并执行新媒体内容选题、撰写和发布；\n3. 与粉丝互动，维护社群活跃度；\n4. 跟踪新媒体数据，定期输出运营分析报告。', '1. 大专及以上学历，1年以上新媒体运营经验；\n2. 熟悉主流新媒体平台规则和玩法；\n3. 具备基础的图文编辑和短视频制作能力；\n4. 思维活跃，有创意，对热点敏感；\n5. 有成功的个人账号或爆款内容案例者优先。', '五险一金、弹性工作、年度体检、年终奖', 0, 2, '2026-08-31 23:59:59', 892, 65, '2026-07-23 09:23:10', '2026-07-23 09:23:10', NULL);
INSERT INTO `boss_job` (`id`, `job_title`, `department`, `workplace`, `experience`, `education`, `salary_range`, `description`, `requirements`, `benefits`, `is_hot`, `job_status`, `expire_at`, `view_count`, `job_sort`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863004423253, '大客户销售经理', '销售中心', '深圳南山区科技园', '3-5年', '本科', '15K-30K·提成', '1. 负责公司核心产品的大客户开拓与销售工作；\n2. 制定销售策略，完成年度销售目标；\n3. 维护重点客户关系，挖掘客户深度需求；\n4. 收集市场情报，反馈客户需求，协助产品优化。', '1. 本科及以上学历，3年以上B2B大客户销售经验；\n2. 具备优秀的沟通表达和商务谈判能力；\n3. 有企业服务、SaaS或软件行业客户资源者优先；\n4. 抗压能力强，能适应短期出差。', '五险一金、提成上不封顶、年度体检、交通补贴、通讯补贴、年终奖', 1, 2, '2026-12-31 23:59:59', 1206, 90, '2026-07-23 09:23:10', '2026-07-23 09:23:10', NULL);
INSERT INTO `boss_job` (`id`, `job_title`, `department`, `workplace`, `experience`, `education`, `salary_range`, `description`, `requirements`, `benefits`, `is_hot`, `job_status`, `expire_at`, `view_count`, `job_sort`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863004423254, '渠道商务专员', '销售中心', '深圳南山区科技园', '1-3年', '大专', '8K-15K·提成', '1. 负责公司产品渠道合作伙伴的拓展与维护；\n2. 制定渠道合作方案，推进渠道签约和落地执行；\n3. 为合作伙伴提供产品培训和销售支持；\n4. 跟踪渠道业绩，定期输出渠道运营报告。', '1. 大专及以上学历，1年以上渠道或商务拓展经验；\n2. 具备良好的沟通协调能力和团队合作精神；\n3. 有IT、软件、互联网行业渠道经验者优先；\n4. 熟练使用Office办公软件，能独立完成商务方案。', '五险一金、提成上不封顶、年度体检、年终奖', 0, 2, '2026-09-15 23:59:59', 367, 70, '2026-07-23 09:23:10', '2026-07-23 09:23:10', NULL);
INSERT INTO `boss_job` (`id`, `job_title`, `department`, `workplace`, `experience`, `education`, `salary_range`, `description`, `requirements`, `benefits`, `is_hot`, `job_status`, `expire_at`, `view_count`, `job_sort`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863004423255, '财务主管', '财务中心', '深圳南山区科技园', '5-8年', '本科', '18K-28K·14薪', '1. 负责公司全盘账务处理，编制财务报表；\n2. 制定和完善财务管理制度和流程；\n3. 负责税务筹划和申报工作；\n4. 参与公司年度预算编制和成本管控；\n5. 配合外部审计和内部审计工作。', '1. 本科及以上学历，财务、会计相关专业，5年以上财务经验；\n2. 持有CPA或中级会计师证书；\n3. 熟悉国家财税法规和企业会计准则；\n4. 熟悉金蝶/用友等财务软件；\n5. 有互联网或科技行业财务经验者优先。', '五险一金、年度体检、弹性工作、年终奖', 0, 2, '2026-08-31 23:59:59', 284, 70, '2026-07-23 09:23:10', '2026-07-23 09:23:10', NULL);
INSERT INTO `boss_job` (`id`, `job_title`, `department`, `workplace`, `experience`, `education`, `salary_range`, `description`, `requirements`, `benefits`, `is_hot`, `job_status`, `expire_at`, `view_count`, `job_sort`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863004423256, '招聘专员', '人力资源中心', '深圳南山区科技园', '1-3年', '本科', '8K-14K·13薪', '1. 负责公司各部门岗位的招聘全流程工作；\n2. 开拓和维护招聘渠道，优化人才库；\n3. 参与校园招聘和雇主品牌建设；\n4. 跟踪招聘数据，定期输出招聘分析报告。', '1. 本科及以上学历，人力资源、心理学等相关专业；\n2. 1年以上招聘经验，有互联网或科技行业招聘经验者优先；\n3. 具备良好的沟通能力和面试技巧；\n4. 熟悉招聘平台（BOSS直聘、拉勾、猎聘等）的使用；\n5. 积极主动，具备较强的执行力和抗压能力。', '五险一金、年度体检、弹性工作、年终奖', 0, 2, '2026-09-30 23:59:59', 523, 65, '2026-07-23 09:23:10', '2026-07-23 09:23:10', NULL);
INSERT INTO `boss_job` (`id`, `job_title`, `department`, `workplace`, `experience`, `education`, `salary_range`, `description`, `requirements`, `benefits`, `is_hot`, `job_status`, `expire_at`, `view_count`, `job_sort`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863004423257, '行政前台', '行政管理中心', '深圳南山区科技园', '1-3年', '大专', '6K-9K·13薪', '1. 负责公司前台接待、电话接听和访客引导；\n2. 负责办公用品采购、发放和库存管理；\n3. 协助组织公司活动、会议和团建；\n4. 负责公司办公环境维护和日常行政事务处理。', '1. 大专及以上学历，1年以上行政或前台经验；\n2. 形象气质佳，沟通表达能力强；\n3. 熟练使用Office办公软件；\n4. 具备良好的服务意识和团队合作精神。', '五险一金、年度体检、节日福利、年终奖', 0, 2, '2026-08-15 23:59:59', 1086, 60, '2026-07-23 09:23:10', '2026-07-23 09:23:10', NULL);
INSERT INTO `boss_job` (`id`, `job_title`, `department`, `workplace`, `experience`, `education`, `salary_range`, `description`, `requirements`, `benefits`, `is_hot`, `job_status`, `expire_at`, `view_count`, `job_sort`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863004423258, 'PHP开发实习生', '技术研发中心', '深圳南山区科技园', '在校生/应届生', '本科', '4K-6K', '1. 参与公司核心业务系统的功能开发和维护；\n2. 在导师指导下完成模块编码、单元测试和文档编写；\n3. 参与技术方案讨论和代码评审；\n4. 学习掌握公司技术栈和开发规范。', '1. 本科及以上学历，计算机相关专业在读或应届毕业生；\n2. 掌握PHP基础语法，了解至少一个主流PHP框架；\n3. 熟悉HTML/CSS/JavaScript基础知识；\n4. 熟悉MySQL基础操作；\n5. 学习能力强，有良好的团队协作精神。', '1对1导师带教、餐补交通补、可转正、实习证明', 0, 2, '2026-08-31 23:59:59', 1432, 55, '2026-07-23 09:23:10', '2026-07-23 09:23:10', NULL);
INSERT INTO `boss_job` (`id`, `job_title`, `department`, `workplace`, `experience`, `education`, `salary_range`, `description`, `requirements`, `benefits`, `is_hot`, `job_status`, `expire_at`, `view_count`, `job_sort`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733863004423259, '测试职位', '技术部', '深圳', '', '', '20k-30k', 'desc', 'req', '五险一金', 0, 2, NULL, 0, 0, '2026-07-26 02:30:20', '2026-07-26 02:30:20', '2026-07-26 02:30:20');
COMMIT;

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  KEY `cache_expiration_index` (`expiration`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cache
-- ----------------------------
BEGIN;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES ('sunny-cloud-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1785074868);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES ('sunny-cloud-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1785074868;', 1785074868);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES ('sunny-cloud-cache-login:0b15c57bb8ab292ad9ba98ac9a561e70b2562b97', 'i:5;', 1785030956);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES ('sunny-cloud-cache-login:0b15c57bb8ab292ad9ba98ac9a561e70b2562b97:timer', 'i:1785030956;', 1785030956);
COMMIT;

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  KEY `cache_locks_expiration_index` (`expiration`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cache_locks
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for category
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `category_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `parent_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '父级分类ID 0是一级分类',
  `show_type` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '可见性类型 0=全部可见 1=指定客户可见 2=指定客户不可见',
  `cat_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态 0=隐藏 1=显示',
  `level` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '级别 1一级 2二级 3三级',
  `sort_order` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `description` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类描述/SEO说明',
  `cat_remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `created_by` bigint unsigned DEFAULT NULL COMMENT '创建人',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `updated_by` bigint unsigned DEFAULT NULL COMMENT '更新人',
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
  `deleted_by` bigint unsigned DEFAULT NULL COMMENT '删除人',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `category_parent_id_index` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=935126090643669000 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='分类表';

-- ----------------------------
-- Records of category
-- ----------------------------
BEGIN;
INSERT INTO `category` (`id`, `category_name`, `parent_id`, `show_type`, `cat_status`, `level`, `sort_order`, `description`, `cat_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (935126090643668993, '视频', 0, 0, 1, 1, 0, '视频网站', '视频网站', '2026-07-22 10:26:47', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `category` (`id`, `category_name`, `parent_id`, `show_type`, `cat_status`, `level`, `sort_order`, `description`, `cat_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (935126090643668994, '技术栈', 0, 0, 1, 1, 0, '程序开发技术栈', '技术栈', '2026-07-22 10:27:44', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `category` (`id`, `category_name`, `parent_id`, `show_type`, `cat_status`, `level`, `sort_order`, `description`, `cat_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (935126090643668995, '电商', 0, 0, 1, 1, 0, '淘宝京东拼多多', '电商网站', '2026-07-22 10:28:46', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `category` (`id`, `category_name`, `parent_id`, `show_type`, `cat_status`, `level`, `sort_order`, `description`, `cat_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (935126090643668996, '公司', 0, 0, 1, 1, 0, '自定义公司本地', '公司', '2026-07-22 10:29:40', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `category` (`id`, `category_name`, `parent_id`, `show_type`, `cat_status`, `level`, `sort_order`, `description`, `cat_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (935126090643668997, '工具', 0, 0, 1, 1, 0, '工具合集', '工具', '2026-07-22 10:30:33', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `category` (`id`, `category_name`, `parent_id`, `show_type`, `cat_status`, `level`, `sort_order`, `description`, `cat_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (935126090643668998, 'AI', 0, 0, 1, 1, 0, 'AI工具', 'AI', '2026-07-22 10:31:04', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `category` (`id`, `category_name`, `parent_id`, `show_type`, `cat_status`, `level`, `sort_order`, `description`, `cat_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (935126090643668999, '搜索', 0, 0, 1, 1, 0, '各类搜索网盘', '网盘搜索', '2026-07-22 10:31:46', NULL, NULL, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for feedbacks
-- ----------------------------
DROP TABLE IF EXISTS `feedbacks`;
CREATE TABLE `feedbacks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fb_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `fb_phone` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT '联系电话',
  `fb_email` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT '邮箱',
  `fb_company` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '' COMMENT '公司名称',
  `fb_title` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '留言标题',
  `fb_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '留言内容',
  `fb_status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0=未处理，1=已处理',
  `reply_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '回复内容',
  `replied_at` datetime DEFAULT NULL COMMENT '回复时间',
  `ip` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT 'IP地址',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_status` (`fb_status`) USING BTREE,
  KEY `idx_created_at` (`created_at`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733863054423257 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='用户留言表';

-- ----------------------------
-- Records of feedbacks
-- ----------------------------
BEGIN;
INSERT INTO `feedbacks` (`id`, `fb_name`, `fb_phone`, `fb_email`, `fb_company`, `fb_title`, `fb_content`, `fb_status`, `reply_content`, `replied_at`, `ip`, `created_at`, `updated_at`) VALUES (920733863054423246, '张伟', '13812345678', 'zhangwei@qq.com', '深圳科技有限公司', '咨询企业版产品功能与报价', '你好，我公司目前正在选型企业级管理系统，看到贵公司的产品介绍后很感兴趣。请问企业版是否支持多租户？能否提供一份详细的功能清单和报价方案？我们预计采购50个用户，希望能在本月底前完成选型。', 0, NULL, NULL, '192.168.1.100', '2026-07-22 09:30:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` (`id`, `fb_name`, `fb_phone`, `fb_email`, `fb_company`, `fb_title`, `fb_content`, `fb_status`, `reply_content`, `replied_at`, `ip`, `created_at`, `updated_at`) VALUES (920733863054423247, '李娜', '15987654321', 'lina@partner.com', '上海云创科技', '寻求渠道合作机会', '我司是专业的IT解决方案服务商，主要服务于华东地区的制造企业。看到贵公司的产品在行业内口碑很好，希望能洽谈渠道代理合作。请安排相关负责人与我联系，谢谢！', 0, NULL, NULL, '10.0.1.50', '2026-07-22 14:20:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` (`id`, `fb_name`, `fb_phone`, `fb_email`, `fb_company`, `fb_title`, `fb_content`, `fb_status`, `reply_content`, `replied_at`, `ip`, `created_at`, `updated_at`) VALUES (920733863054423248, '王小明', '13524681357', 'wangxm@tech.com', '北京创新科技', '系统升级后数据无法同步', '我们使用的是贵公司产品3.0版本，上周升级后，发现部分数据无法正常同步到云端。已尝试重启服务但问题依旧，麻烦尽快安排技术人员协助排查，我们这边业务受到了影响。', 0, NULL, NULL, '172.16.0.30', '2026-07-21 16:45:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` (`id`, `fb_name`, `fb_phone`, `fb_email`, `fb_company`, `fb_title`, `fb_content`, `fb_status`, `reply_content`, `replied_at`, `ip`, `created_at`, `updated_at`) VALUES (920733863054423249, '陈静', '13698765432', 'chenjing@mail.com', '广州越秀集团', '建议增加多语言支持功能', '我们是跨国企业，目前贵公司产品仅支持中英文，建议在后续版本中增加对日语和韩语的支持，方便我们海外团队使用。如果能提供多语言切换功能就更好了。期待产品越来越好！', 0, NULL, NULL, '192.168.2.80', '2026-07-21 11:00:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` (`id`, `fb_name`, `fb_phone`, `fb_email`, `fb_company`, `fb_title`, `fb_content`, `fb_status`, `reply_content`, `replied_at`, `ip`, `created_at`, `updated_at`) VALUES (920733863054423250, '刘洋', '15234567890', 'liuyang@outlook.com', '', '咨询PHP开发岗位招聘信息', '您好！我看到贵公司招聘PHP高级开发工程师，我拥有6年PHP开发经验，熟悉Hyperf和ThinkPHP框架。想了解该岗位是否还在招人？以及面试流程是怎样的？期待您的回复！', 0, NULL, NULL, '10.0.2.15', '2026-07-20 09:00:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` (`id`, `fb_name`, `fb_phone`, `fb_email`, `fb_company`, `fb_title`, `fb_content`, `fb_status`, `reply_content`, `replied_at`, `ip`, `created_at`, `updated_at`) VALUES (920733863054423251, '赵磊', '13765432198', 'zhaolei@abc.com', '杭州云创科技', '咨询产品价格与部署方案', '我司正在寻找一套企业级CRM解决方案，想咨询贵公司产品的价格体系和部署方式。同时希望能安排一次在线演示，让我们团队了解一下产品的实际使用体验。', 1, '尊敬的赵先生，您好！感谢您对产品的关注。相关产品资料和报价方案已发送至您的邮箱，并已安排销售顾问于明日10:00与您联系，届时将为您做详细的产品演示。如有其他问题，可随时联系我们的客服热线。祝工作顺利！', '2026-07-21 17:30:00', '192.168.3.20', '2026-07-19 15:00:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` (`id`, `fb_name`, `fb_phone`, `fb_email`, `fb_company`, `fb_title`, `fb_content`, `fb_status`, `reply_content`, `replied_at`, `ip`, `created_at`, `updated_at`) VALUES (920733863054423252, '孙婷', '15824681357', 'sunting@qq.com', '成都互联科技', '希望能成为贵公司渠道伙伴', '我们公司在西南地区深耕企业服务多年，拥有超过500家客户资源。希望能成为贵公司在西南地区的渠道合作伙伴，共同开拓市场。请告知合作条件和流程。', 1, '尊敬的孙总，您好！非常欢迎合作意向。我们已安排渠道部负责人在今日内与您联系，详细沟通合作细节。同时，相关合作政策已发至您的邮箱，请您查收。期待我们的合作！', '2026-07-21 10:00:00', '192.168.4.40', '2026-07-19 11:30:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` (`id`, `fb_name`, `fb_phone`, `fb_email`, `fb_company`, `fb_title`, `fb_content`, `fb_status`, `reply_content`, `replied_at`, `ip`, `created_at`, `updated_at`) VALUES (920733863054423253, '钱森', '13987654321', 'qiansen@tech.com', '苏州智能制造', '产品使用遇到性能瓶颈', '我们是贵公司的老客户，使用产品已有一年多。最近随着业务量增长，系统响应速度明显变慢。请问是否有性能优化的方案或建议？希望能得到专业指导。', 1, '钱先生，您好！感谢您长期以来的支持。针对您反馈的性能问题，建议从以下几个方面进行优化：1. 检查数据库索引配置；2. 开启缓存功能；3. 升级到最新版本（3.1版本已对性能有较大提升）。我们已在后台为您的账户开启了高级技术支持通道，相关优化文档也已发送至您的邮箱。如有疑问可随时联系我们。', '2026-07-20 14:30:00', '10.0.3.60', '2026-07-18 08:30:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` (`id`, `fb_name`, `fb_phone`, `fb_email`, `fb_company`, `fb_title`, `fb_content`, `fb_status`, `reply_content`, `replied_at`, `ip`, `created_at`, `updated_at`) VALUES (920733863054423254, '周玥', '15012345678', 'zhouyue@mail.com', '厦门科技集团', '产品体验很好，特别感谢', '您好！我是贵公司的产品用户，最近使用产品感觉非常好，界面简洁，功能实用，极大提升了我们的工作效率。感谢你们团队做出这么优秀的产品！特此留言表达感谢。', 1, '周女士，您好！非常感谢您的认可和鼓励。客户的满意是我们最大的动力！我们会继续打磨产品，为您提供更好的使用体验。如有任何建议或需求，欢迎随时联系我们。祝您工作愉快！', '2026-07-20 09:00:00', '192.168.5.70', '2026-07-17 16:00:00', '2026-07-23 09:51:53');
INSERT INTO `feedbacks` (`id`, `fb_name`, `fb_phone`, `fb_email`, `fb_company`, `fb_title`, `fb_content`, `fb_status`, `reply_content`, `replied_at`, `ip`, `created_at`, `updated_at`) VALUES (920733863054423255, '吴强', '18965432109', 'wuqiang@tech.com', '武汉高新科技', '咨询产品定制化开发服务', '我公司需要一套定制化的企业管理系统，想咨询贵公司是否接受定制化开发？一般定制周期和费用大概是怎样的？希望能得到专业的解答。', 1, '已收到您的留言，我们会尽快跟进。', '2026-07-26 02:30:20', '172.16.1.25', '2026-07-16 10:30:00', '2026-07-26 02:30:20');
INSERT INTO `feedbacks` (`id`, `fb_name`, `fb_phone`, `fb_email`, `fb_company`, `fb_title`, `fb_content`, `fb_status`, `reply_content`, `replied_at`, `ip`, `created_at`, `updated_at`) VALUES (920733863054423256, '郭靖', '130266611119', '', '赛格科技', '应聘：PHP高级开发工程师', '您好，我对「PHP高级开发工程师」（技术研发中心 / 深圳南山区科技园）职位感兴趣，期待沟通。', 0, NULL, NULL, '127.0.0.1', '2026-07-26 14:06:48', '2026-07-26 14:06:48');
COMMIT;

-- ----------------------------
-- Table structure for friend_links
-- ----------------------------
DROP TABLE IF EXISTS `friend_links`;
CREATE TABLE `friend_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `link_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '网站名称',
  `link_url` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '网站链接',
  `link_logo` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '网站Logo',
  `link_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '网站描述',
  `link_sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序越小越前',
  `link_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '0=禁用，1=启用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733863055413257 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='友情链接表';

-- ----------------------------
-- Records of friend_links
-- ----------------------------
BEGIN;
INSERT INTO `friend_links` (`id`, `link_name`, `link_url`, `link_logo`, `link_desc`, `link_sort`, `link_status`, `created_at`, `updated_at`) VALUES (920733863055413246, '百度', 'https://www.baidu.com', '/uploads/friendlinks/baidu.png', '全球最大的中文搜索引擎', 1, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` (`id`, `link_name`, `link_url`, `link_logo`, `link_desc`, `link_sort`, `link_status`, `created_at`, `updated_at`) VALUES (920733863055413247, '腾讯云', 'https://cloud.tencent.com', '/uploads/friendlinks/tencent-cloud.png', '腾讯云 - 产业智变，云启未来', 2, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` (`id`, `link_name`, `link_url`, `link_logo`, `link_desc`, `link_sort`, `link_status`, `created_at`, `updated_at`) VALUES (920733863055413248, '阿里云', 'https://www.aliyun.com', '/uploads/friendlinks/aliyun.png', '阿里云 - 上云就上阿里云', 3, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` (`id`, `link_name`, `link_url`, `link_logo`, `link_desc`, `link_sort`, `link_status`, `created_at`, `updated_at`) VALUES (920733863055413249, '华为云', 'https://www.huaweicloud.com', '/uploads/friendlinks/huawei-cloud.png', '华为云 - 选择华为云，让您的业务更上一层楼', 4, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` (`id`, `link_name`, `link_url`, `link_logo`, `link_desc`, `link_sort`, `link_status`, `created_at`, `updated_at`) VALUES (920733863055413250, 'CSDN', 'https://www.csdn.net', '/uploads/friendlinks/csdn.png', 'CSDN - 专业开发者技术社区', 5, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` (`id`, `link_name`, `link_url`, `link_logo`, `link_desc`, `link_sort`, `link_status`, `created_at`, `updated_at`) VALUES (920733863055413251, '掘金', 'https://juejin.cn', '/uploads/friendlinks/juejin.png', '掘金 - 一个帮助开发者成长的社区', 6, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` (`id`, `link_name`, `link_url`, `link_logo`, `link_desc`, `link_sort`, `link_status`, `created_at`, `updated_at`) VALUES (920733863055413252, '开源中国', 'https://www.oschina.net', '/uploads/friendlinks/oschina.png', '开源中国 - 中国最大的开源技术社区', 7, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` (`id`, `link_name`, `link_url`, `link_logo`, `link_desc`, `link_sort`, `link_status`, `created_at`, `updated_at`) VALUES (920733863055413253, 'SegmentFault 思否', 'https://segmentfault.com', '/uploads/friendlinks/segmentfault.png', 'SegmentFault - 技术问答社区', 8, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` (`id`, `link_name`, `link_url`, `link_logo`, `link_desc`, `link_sort`, `link_status`, `created_at`, `updated_at`) VALUES (920733863055413254, '牛客网', 'https://www.nowcoder.com', '/uploads/friendlinks/nowcoder.png', '牛客网 - 求职招聘与校招笔试面试平台', 9, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
INSERT INTO `friend_links` (`id`, `link_name`, `link_url`, `link_logo`, `link_desc`, `link_sort`, `link_status`, `created_at`, `updated_at`) VALUES (920733863055413255, '拉勾网', 'https://www.lagou.com', '/uploads/friendlinks/lagou.png', '拉勾网 - 专注互联网招聘的招聘平台', 10, 1, '2026-07-23 09:24:33', '2026-07-23 09:24:33');
COMMIT;

-- ----------------------------
-- Table structure for hr_department
-- ----------------------------
DROP TABLE IF EXISTS `hr_department`;
CREATE TABLE `hr_department` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '部门主键ID',
  `parent_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '父部门ID，0=根节点（总公司）',
  `dept_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '部门名称：总公司、深圳分公司、技术部、财务部、商品运营组',
  `dept_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '部门唯一编码，程序权限/审批规则使用',
  `ancestors` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '祖先ID路径，逗号分隔：0,1,5，用于快速查上级/所有下级，冗余字段提升性能',
  `dept_level` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '层级深度：1根节点、2一级部门、3二级小组',
  `leader_user_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '部门负责人ID（关联sys_user.id），审批可直接取部门负责人',
  `dept_phone` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '部门联系电话',
  `dept_sort` int NOT NULL DEFAULT '0' COMMENT '树形展示排序号',
  `dept_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0禁用 1正常启用',
  `created_by` bigint unsigned NOT NULL DEFAULT '0' COMMENT '创建人用户ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_dept_code` (`dept_code`) USING BTREE,
  KEY `idx_parent_id` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733862005400006 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='企业组织架构部门表';

-- ----------------------------
-- Records of hr_department
-- ----------------------------
BEGIN;
INSERT INTO `hr_department` (`id`, `parent_id`, `dept_name`, `dept_code`, `ancestors`, `dept_level`, `leader_user_id`, `dept_phone`, `dept_sort`, `dept_status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862005400000, 0, '集团总公司', 'group_all', '0', 1, 0, '', 1, 1, 0, '2026-07-25 02:19:47', '2026-07-25 02:19:47', NULL);
INSERT INTO `hr_department` (`id`, `parent_id`, `dept_name`, `dept_code`, `ancestors`, `dept_level`, `leader_user_id`, `dept_phone`, `dept_sort`, `dept_status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862005400001, 920733862005400000, '深圳分公司', 'branch_shenzhen', '0,920733862005400000', 2, 920733860755423008, '', 1, 1, 0, '2026-07-25 02:19:47', '2026-07-26 02:42:16', NULL);
INSERT INTO `hr_department` (`id`, `parent_id`, `dept_name`, `dept_code`, `ancestors`, `dept_level`, `leader_user_id`, `dept_phone`, `dept_sort`, `dept_status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862005400002, 920733862005400001, '财务部', 'dept_finance', '0,920733862005400000,920733862005400001', 3, 920733860755423009, '', 1, 1, 0, '2026-07-25 02:19:47', '2026-07-26 02:39:59', NULL);
INSERT INTO `hr_department` (`id`, `parent_id`, `dept_name`, `dept_code`, `ancestors`, `dept_level`, `leader_user_id`, `dept_phone`, `dept_sort`, `dept_status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862005400003, 920733862005400001, '运营部', 'dept_operation', '0,920733862005400000,920733862005400002', 3, 10003, '', 2, 1, 0, '2026-07-25 02:19:47', '2026-07-25 02:22:35', NULL);
INSERT INTO `hr_department` (`id`, `parent_id`, `dept_name`, `dept_code`, `ancestors`, `dept_level`, `leader_user_id`, `dept_phone`, `dept_sort`, `dept_status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862005400004, 920733862005400003, '商品运营小组', 'group_product', '0,920733862005400000,920733862005400002,920733862005400003', 4, 920733860755423005, '', 1, 1, 0, '2026-07-25 02:19:47', '2026-07-26 02:40:09', NULL);
INSERT INTO `hr_department` (`id`, `parent_id`, `dept_name`, `dept_code`, `ancestors`, `dept_level`, `leader_user_id`, `dept_phone`, `dept_sort`, `dept_status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862005400005, 920733862005400001, '技术部', 'dept_it', '0,920733862005400000,920733862005400001', 1, 934035802554576896, '', 0, 1, 0, '2026-07-25 03:09:02', '2026-07-26 02:39:42', NULL);
COMMIT;

-- ----------------------------
-- Table structure for hr_dept_leaders
-- ----------------------------
DROP TABLE IF EXISTS `hr_dept_leaders`;
CREATE TABLE `hr_dept_leaders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dept_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '组织ID',
  `user_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '负责人ID',
  `role_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '负责人类型：1主要负责人，2次要负责人',
  `created_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL COMMENT '更新人',
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `index_dept_id` (`dept_id`) USING BTREE,
  KEY `index_user_id` (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733860747034693 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='部门负责人表';

-- ----------------------------
-- Records of hr_dept_leaders
-- ----------------------------
BEGIN;
INSERT INTO `hr_dept_leaders` (`id`, `dept_id`, `user_id`, `role_type`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733860747034689, 920733862005400005, 934035802554576896, 1, '2026-07-26 02:39:42', 934035802554576897, '2026-07-26 02:39:42', 934035802554576897, NULL, NULL);
INSERT INTO `hr_dept_leaders` (`id`, `dept_id`, `user_id`, `role_type`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733860747034690, 920733862005400002, 920733860755423009, 1, '2026-07-26 02:39:59', 934035802554576897, '2026-07-26 02:39:59', 934035802554576897, NULL, NULL);
INSERT INTO `hr_dept_leaders` (`id`, `dept_id`, `user_id`, `role_type`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733860747034691, 920733862005400004, 920733860755423005, 1, '2026-07-26 02:40:09', 934035802554576897, '2026-07-26 02:40:09', 934035802554576897, NULL, NULL);
INSERT INTO `hr_dept_leaders` (`id`, `dept_id`, `user_id`, `role_type`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733860747034692, 920733862005400001, 920733860755423008, 1, '2026-07-26 02:42:16', 934035802554576897, '2026-07-26 02:42:16', 934035802554576897, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for hr_post
-- ----------------------------
DROP TABLE IF EXISTS `hr_post`;
CREATE TABLE `hr_post` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '岗位主键ID',
  `parent_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '父级岗位ID，0=顶级根岗位',
  `post_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '岗位名称：总经理、部门经理、技术主管、前端开发、财务专员',
  `post_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '岗位唯一编码（用于代码判断、审批规则）',
  `post_sort` int NOT NULL DEFAULT '0' COMMENT '排序号',
  `post_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0=禁用 1=正常启用',
  `remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '岗位描述备注',
  `created_by` bigint unsigned NOT NULL DEFAULT '0' COMMENT '创建人用户ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_post_code` (`post_code`) USING BTREE,
  KEY `idx_parent_id` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733862004423256 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='人事岗位表';

-- ----------------------------
-- Records of hr_post
-- ----------------------------
BEGIN;
INSERT INTO `hr_post` (`id`, `parent_id`, `post_name`, `post_code`, `post_sort`, `post_status`, `remark`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862004423246, 0, '总经理', 'general_manager', 1, 1, '公司最高负责人，终审审批', 0, '2026-07-25 02:10:13', '2026-07-25 02:10:13', NULL);
INSERT INTO `hr_post` (`id`, `parent_id`, `post_name`, `post_code`, `post_sort`, `post_status`, `remark`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862004423247, 920733862004423246, '部门经理', 'dept_manager', 2, 1, '各部门负责人，一级审批', 0, '2026-07-25 02:10:13', '2026-07-25 02:11:03', NULL);
INSERT INTO `hr_post` (`id`, `parent_id`, `post_name`, `post_code`, `post_sort`, `post_status`, `remark`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862004423248, 920733862004423247, '技术主管', 'tech_supervisor', 3, 1, '技术团队管理', 0, '2026-07-25 02:10:13', '2026-07-25 02:11:03', NULL);
INSERT INTO `hr_post` (`id`, `parent_id`, `post_name`, `post_code`, `post_sort`, `post_status`, `remark`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862004423249, 920733862004423247, '财务专员', 'finance_staff', 4, 1, '费用报销、付款审核', 0, '2026-07-25 02:10:13', '2026-07-25 02:11:04', NULL);
INSERT INTO `hr_post` (`id`, `parent_id`, `post_name`, `post_code`, `post_sort`, `post_status`, `remark`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862004423250, 920733862004423247, '采购专员', 'purchase_staff', 5, 1, '采购申请发起与初审', 0, '2026-07-25 02:10:13', '2026-07-25 02:11:04', NULL);
INSERT INTO `hr_post` (`id`, `parent_id`, `post_name`, `post_code`, `post_sort`, `post_status`, `remark`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862004423251, 920733862004423247, '运营专员', 'operation_staff', 6, 1, '商品上架、客户入驻初审', 0, '2026-07-25 02:10:13', '2026-07-25 02:11:05', NULL);
INSERT INTO `hr_post` (`id`, `parent_id`, `post_name`, `post_code`, `post_sort`, `post_status`, `remark`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862004423252, 920733862004423248, '后端工程师', 'back', 7, 1, '', 0, '2026-07-25 02:12:05', '2026-07-25 02:13:02', NULL);
INSERT INTO `hr_post` (`id`, `parent_id`, `post_name`, `post_code`, `post_sort`, `post_status`, `remark`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862004423255, 920733862004423248, '前端工程师', 'front', 8, 1, '', 0, '2026-07-25 02:12:51', '2026-07-25 02:13:02', NULL);
COMMIT;

-- ----------------------------
-- Table structure for hr_user_dept_post
-- ----------------------------
DROP TABLE IF EXISTS `hr_user_dept_post`;
CREATE TABLE `hr_user_dept_post` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '员工ID，关联sys_user.id',
  `dept_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '所属部门ID，关联sys_department.id',
  `post_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '兼任岗位ID，关联sys_post.id',
  `is_main` tinyint NOT NULL DEFAULT '0' COMMENT '是否为主岗位/主部门 0=兼职 1=本职主岗',
  `remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '兼任说明-同一个员工不能在同一个部门重复挂同一个岗位',
  `start_at` datetime DEFAULT NULL COMMENT '任职开始日期',
  `end_at` datetime DEFAULT NULL COMMENT '任职结束日期，离职/调岗则填充',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_user_dept_post` (`user_id`,`dept_id`,`post_id`) USING BTREE,
  KEY `idx_user_id` (`user_id`) USING BTREE,
  KEY `idx_dept_id` (`dept_id`) USING BTREE,
  KEY `idx_post_id` (`post_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='员工-部门-岗位多兼任关联表';

-- ----------------------------
-- Records of hr_user_dept_post
-- ----------------------------
BEGIN;
INSERT INTO `hr_user_dept_post` (`id`, `user_id`, `dept_id`, `post_id`, `is_main`, `remark`, `start_at`, `end_at`, `created_at`, `updated_at`) VALUES (1, 934035802554576896, 920733862005400001, 920733862004423252, 1, '', '2026-07-01 00:00:00', NULL, '2026-07-25 03:07:33', '2026-07-25 03:10:46');
INSERT INTO `hr_user_dept_post` (`id`, `user_id`, `dept_id`, `post_id`, `is_main`, `remark`, `start_at`, `end_at`, `created_at`, `updated_at`) VALUES (2, 934035802554576898, 920733862005400001, 920733862004423255, 1, '', '2026-05-01 00:00:00', NULL, '2026-07-25 03:07:33', '2026-07-25 03:10:20');
INSERT INTO `hr_user_dept_post` (`id`, `user_id`, `dept_id`, `post_id`, `is_main`, `remark`, `start_at`, `end_at`, `created_at`, `updated_at`) VALUES (3, 920733860755423005, 920733862005400002, 920733862004423249, 0, '', '2026-07-01 00:00:00', NULL, '2026-07-26 02:40:47', '2026-07-26 02:40:47');
INSERT INTO `hr_user_dept_post` (`id`, `user_id`, `dept_id`, `post_id`, `is_main`, `remark`, `start_at`, `end_at`, `created_at`, `updated_at`) VALUES (4, 920733860755423009, 920733862005400005, 920733862004423252, 0, '', '2026-07-01 00:00:00', NULL, '2026-07-26 02:41:14', '2026-07-26 02:41:14');
COMMIT;

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of job_batches
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `jobs_queue_index` (`queue`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of jobs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of migrations
-- ----------------------------
BEGIN;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4, '2026_07_24_000001_create_category_table', 2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5, '2026_07_24_000002_create_user_account_table', 999);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6, '2026_07_24_000003_create_auth_menu_table', 999);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7, '2026_07_24_000004_create_auth_role_table', 999);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8, '2026_07_24_000005_create_auth_permission_table', 999);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9, '2026_07_24_000006_create_auth_relation_tables', 999);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10, '2026_07_25_000001_create_hr_department_table', 1000);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11, '2026_07_25_000002_create_hr_post_table', 1000);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12, '2026_07_25_000003_create_hr_dept_leaders_table', 1000);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13, '2026_07_25_000004_create_hr_user_dept_post_table', 1000);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14, '2026_07_25_000010_create_product_brand_table', 1001);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15, '2026_07_25_000011_create_product_category_table', 1001);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16, '2026_07_25_000012_create_product_specification_table', 1001);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17, '2026_07_25_000013_create_product_specification_value_table', 1001);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18, '2026_07_25_000020_create_product_table', 1002);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19, '2026_07_25_000021_create_product_media_table', 1002);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20, '2026_07_25_000022_create_product_sku_table', 1002);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21, '2026_07_25_000023_create_product_sku_spec_value_table', 1002);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22, '2026_07_25_000030_create_wf_flow_type_table', 1003);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23, '2026_07_25_000031_create_wf_flow_definition_table', 1003);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24, '2026_07_25_000032_create_wf_flow_form_table', 1003);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25, '2026_07_25_000033_create_wf_flow_node_table', 1003);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26, '2026_07_25_000034_create_wf_flow_node_condition_table', 1004);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27, '2026_07_25_000035_create_wf_flow_apply_table', 1004);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28, '2026_07_25_000036_create_wf_flow_approve_record_table', 1004);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29, '2026_07_25_000037_create_wf_flow_cc_user_table', 1004);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30, '2026_07_26_000001_create_operation_log_table', 1005);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31, '2026_07_26_000010_create_article_category_table', 1006);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32, '2026_07_26_000011_create_articles_table', 1006);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33, '2026_07_26_000020_create_book_mark_table', 1007);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34, '2026_07_26_000030_create_feedbacks_table', 1008);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35, '2026_07_26_000031_create_boss_job_table', 1008);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36, '2026_07_26_000040_create_friend_links_table', 1009);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37, '2026_07_26_000041_create_site_configs_table', 1009);
COMMIT;

-- ----------------------------
-- Table structure for operation_log
-- ----------------------------
DROP TABLE IF EXISTS `operation_log`;
CREATE TABLE `operation_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `operator_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '操作人ID',
  `operator_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '操作人名称',
  `biz_type` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '业务模块类型 product/category/customer',
  `activity_type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '活动类型如product_created',
  `action` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '操作类型 (INSERT/UPDATE/DELETE/LOGIN)',
  `biz_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '目标实体ID',
  `biz_label` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '高亮展示文本',
  `old_value` json DEFAULT NULL COMMENT '修改前的数据快照 (JSON格式)',
  `new_value` json DEFAULT NULL COMMENT '修改后的数据快照 (JSON格式)',
  `operator_status` tinyint NOT NULL DEFAULT '1' COMMENT '操作状态 (0:失败, 1:成功)',
  `error_msg` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '错误信息 (失败时记录)',
  `client_ip` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '客户端IP',
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户浏览器/设备信息',
  `request_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '触发日志的API URL',
  `method_fun` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '触发日志的方法名',
  `created_at` datetime(6) DEFAULT NULL COMMENT '发生时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `merchant_activity_log_operator_id_index` (`operator_id`) USING BTREE,
  KEY `merchant_activity_log_biz_index` (`biz_type`,`biz_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=935126090643669085 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='用户操作动态表';

-- ----------------------------
-- Records of operation_log
-- ----------------------------
BEGIN;
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669063, 0, 'admin', 'auth', 'user_login_failed', 'LOGIN', 0, 'admin', NULL, '{\"guard\": \"backend\", \"account\": \"admin\"}', 0, '账号或密码错误', '127.0.0.1', 'Symfony', 'http://localhost/backend/api/auth/login', 'AuthController@login', '2026-07-26 02:04:53.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669064, 0, 'admin', 'auth', 'user_login_failed', 'LOGIN', 0, 'admin', NULL, '{\"guard\": \"backend\", \"account\": \"admin\"}', 0, '账号或密码错误', '127.0.0.1', 'Symfony', 'http://localhost/backend/api/auth/login', 'AuthController@login', '2026-07-26 02:04:54.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669065, 0, 'admin', 'auth', 'user_login_failed', 'LOGIN', 0, 'admin', NULL, '{\"guard\": \"backend\", \"account\": \"admin\"}', 0, '账号或密码错误', '127.0.0.1', 'Symfony', 'http://localhost/backend/api/auth/login', 'AuthController@login', '2026-07-26 02:04:54.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669066, 0, '', 'product_brand', 'product_brand_created', 'INSERT', 1, '测试品牌', NULL, '{\"brand_name\": \"测试品牌\"}', 1, '', '127.0.0.1', 'Symfony', 'http://localhost:8000', 'cli@test', '2026-07-26 02:05:03.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669067, 934035802554576897, '管理员', 'auth', 'user_login', 'LOGIN', 934035802554576897, 'admin', NULL, '{\"guard\": \"backend\", \"account\": \"admin\"}', 1, '', '127.0.0.1', 'Symfony', 'http://localhost:8000', 'AuthController@login', '2026-07-26 02:05:08.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669068, 934035802554576897, '管理员', 'product_brand', 'product_brand_created', 'INSERT', 920733862755000004, 'cesd', NULL, '{\"id\": \"920733862755000004\", \"alias\": \"sdd\", \"is_show\": 1, \"is_system\": 0, \"brand_code\": \"BR000005\", \"brand_name\": \"cesd\", \"sort_order\": 0, \"brand_remark\": \"\", \"is_show_label\": \"显示\", \"is_system_label\": \"自定义\"}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/backend/api/product/brands', 'ProductBrandService@create', '2026-07-26 02:08:16.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669069, 934035802554576897, '管理员', 'product_brand', 'product_brand_deleted', 'DELETE', 920733862755000004, 'cesd', '{\"id\": \"920733862755000004\", \"alias\": \"sdd\", \"is_show\": 1, \"is_system\": 0, \"brand_code\": \"BR000005\", \"brand_name\": \"cesd\", \"sort_order\": 0, \"brand_remark\": \"\", \"is_show_label\": \"显示\", \"is_system_label\": \"自定义\"}', NULL, 1, '', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/backend/api/product/brands/920733862755000004', 'ProductBrandService@delete', '2026-07-26 02:08:36.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669070, 934035802554576897, '管理员', 'article', 'article_created', 'INSERT', 920733863034423262, '草稿测试', NULL, '{\"id\": \"920733863034423262\", \"title\": \"草稿测试\", \"is_top\": 0, \"source\": \"\", \"summary\": \"hello\", \"tag_ids\": [], \"subtitle\": \"\", \"art_cover\": \"\", \"author_id\": \"934035802554576897\", \"seo_title\": \"\", \"art_status\": 1, \"created_at\": \"2026-07-26 02:16:53\", \"like_count\": 0, \"source_url\": \"\", \"updated_at\": \"2026-07-26 02:16:53\", \"view_count\": 0, \"art_content\": \"<p>hello</p>\", \"author_name\": \"管理员\", \"category_id\": \"920733863034423253\", \"is_original\": 1, \"reviewed_at\": null, \"reviewer_id\": \"\", \"share_count\": 0, \"content_type\": 1, \"extra_fields\": null, \"is_top_label\": \"否\", \"published_at\": null, \"seo_keywords\": \"\", \"category_name\": \"企业公告\", \"collect_count\": 0, \"comment_count\": 0, \"reject_reason\": null, \"is_commentable\": 1, \"seo_description\": \"\", \"art_status_label\": \"草稿\", \"is_original_label\": \"是\", \"content_type_label\": \"富文本\", \"is_commentable_label\": \"是\"}', 1, '', '127.0.0.1', 'Symfony', 'http://localhost:8000', 'ArticleService@create', '2026-07-26 02:16:53.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669071, 934035802554576897, '管理员', 'article', 'article_status_updated', 'UPDATE', 920733863034423262, '草稿测试', '{\"id\": \"920733863034423262\", \"title\": \"草稿测试\", \"is_top\": 0, \"source\": \"\", \"summary\": \"hello\", \"tag_ids\": [], \"subtitle\": \"\", \"art_cover\": \"\", \"author_id\": \"934035802554576897\", \"seo_title\": \"\", \"art_status\": 1, \"created_at\": \"2026-07-26 02:16:53\", \"like_count\": 0, \"source_url\": \"\", \"updated_at\": \"2026-07-26 02:16:53\", \"view_count\": 0, \"art_content\": \"<p>hello</p>\", \"author_name\": \"管理员\", \"category_id\": \"920733863034423253\", \"is_original\": 1, \"reviewed_at\": null, \"reviewer_id\": \"\", \"share_count\": 0, \"content_type\": 1, \"extra_fields\": null, \"is_top_label\": \"否\", \"published_at\": null, \"seo_keywords\": \"\", \"category_name\": \"企业公告\", \"collect_count\": 0, \"comment_count\": 0, \"reject_reason\": null, \"is_commentable\": 1, \"seo_description\": \"\", \"art_status_label\": \"草稿\", \"is_original_label\": \"是\", \"content_type_label\": \"富文本\", \"is_commentable_label\": \"是\"}', '{\"id\": \"920733863034423262\", \"title\": \"草稿测试\", \"is_top\": 0, \"source\": \"\", \"summary\": \"hello\", \"tag_ids\": [], \"subtitle\": \"\", \"art_cover\": \"\", \"author_id\": \"934035802554576897\", \"seo_title\": \"\", \"art_status\": 4, \"created_at\": \"2026-07-26 02:16:53\", \"like_count\": 0, \"source_url\": \"\", \"updated_at\": \"2026-07-26 02:16:53\", \"view_count\": 0, \"art_content\": \"<p>hello</p>\", \"author_name\": \"管理员\", \"category_id\": \"920733863034423253\", \"is_original\": 1, \"reviewed_at\": \"2026-07-26 02:16:53\", \"reviewer_id\": \"934035802554576897\", \"share_count\": 0, \"content_type\": 1, \"extra_fields\": null, \"is_top_label\": \"否\", \"published_at\": \"2026-07-26 02:16:53\", \"seo_keywords\": \"\", \"category_name\": \"企业公告\", \"collect_count\": 0, \"comment_count\": 0, \"reject_reason\": null, \"is_commentable\": 1, \"seo_description\": \"\", \"art_status_label\": \"已发布\", \"is_original_label\": \"是\", \"content_type_label\": \"富文本\", \"is_commentable_label\": \"是\"}', 1, '', '127.0.0.1', 'Symfony', 'http://localhost:8000', 'ArticleService@updateStatus', '2026-07-26 02:16:53.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669072, 934035802554576897, '管理员', 'book_mark', 'book_mark_created', 'INSERT', 934041315296100356, '书签测试', NULL, '{\"id\": \"934041315296100356\", \"status\": 1, \"is_bold\": 0, \"book_url\": \"https://example.com\", \"book_desc\": \"demo\", \"book_title\": \"书签测试\", \"created_at\": \"2026-07-26 02:22:30\", \"created_by\": \"934035802554576897\", \"sort_order\": 1, \"updated_at\": \"2026-07-26 02:22:30\", \"category_id\": \"935126090643668998\", \"short_title\": \"测试\", \"book_favicon\": \"\", \"status_label\": \"正常\", \"category_name\": \"AI\", \"is_bold_label\": \"加粗\"}', 1, '', '127.0.0.1', 'Symfony', 'http://localhost:8000', 'BookMarkService@create', '2026-07-26 02:22:30.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669073, 934035802554576897, '管理员', 'book_mark', 'book_mark_deleted', 'DELETE', 934041315296100356, '书签测试', '{\"id\": \"934041315296100356\", \"status\": 0, \"is_bold\": 0, \"book_url\": \"https://example.com\", \"book_desc\": \"demo\", \"book_title\": \"书签测试\", \"created_at\": \"2026-07-26 02:22:30\", \"created_by\": \"934035802554576897\", \"sort_order\": 1, \"updated_at\": \"2026-07-26 02:22:30\", \"category_id\": \"935126090643668998\", \"short_title\": \"测试\", \"book_favicon\": \"\", \"status_label\": \"隐藏\", \"category_name\": \"AI\", \"is_bold_label\": \"加粗\"}', NULL, 1, '', '127.0.0.1', 'Symfony', 'http://localhost:8000', 'BookMarkService@delete', '2026-07-26 02:22:30.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669074, 0, '', 'feedback', 'feedback_replied', 'UPDATE', 920733863054423255, '咨询产品定制化开发服务', '{\"id\": \"920733863054423255\", \"ip\": \"172.16.1.25\", \"fb_name\": \"吴强\", \"fb_email\": \"wuqiang@tech.com\", \"fb_phone\": \"18965432109\", \"fb_title\": \"咨询产品定制化开发服务\", \"fb_status\": 1, \"created_at\": \"2026-07-16 10:30:00\", \"fb_company\": \"武汉高新科技\", \"fb_content\": \"我公司需要一套定制化的企业管理系统，想咨询贵公司是否接受定制化开发？一般定制周期和费用大概是怎样的？希望能得到专业的解答。\", \"replied_at\": \"2026-07-19 16:00:00\", \"updated_at\": \"2026-07-23 09:51:53\", \"reply_content\": \"吴先生，您好！感谢您的咨询。关于定制化开发服务，已有专人整理详细方案并发送至您的邮箱。销售经理会在明日与您电话沟通具体需求细节。请注意查收邮件，感谢您的关注！\", \"fb_status_label\": \"已处理\"}', '{\"id\": \"920733863054423255\", \"ip\": \"172.16.1.25\", \"fb_name\": \"吴强\", \"fb_email\": \"wuqiang@tech.com\", \"fb_phone\": \"18965432109\", \"fb_title\": \"咨询产品定制化开发服务\", \"fb_status\": 1, \"created_at\": \"2026-07-16 10:30:00\", \"fb_company\": \"武汉高新科技\", \"fb_content\": \"我公司需要一套定制化的企业管理系统，想咨询贵公司是否接受定制化开发？一般定制周期和费用大概是怎样的？希望能得到专业的解答。\", \"replied_at\": \"2026-07-26 02:30:20\", \"updated_at\": \"2026-07-26 02:30:20\", \"reply_content\": \"已收到您的留言，我们会尽快跟进。\", \"fb_status_label\": \"已处理\"}', 1, '', '127.0.0.1', 'Symfony', 'http://localhost:8000', 'FeedbackService@reply', '2026-07-26 02:30:20.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669075, 0, '', 'boss_job', 'boss_job_created', 'INSERT', 920733863004423259, '测试职位', NULL, '{\"id\": \"920733863004423259\", \"is_hot\": 1, \"benefits\": \"五险一金\", \"job_sort\": 0, \"education\": \"\", \"expire_at\": null, \"job_title\": \"测试职位\", \"workplace\": \"深圳\", \"created_at\": \"2026-07-26 02:30:20\", \"department\": \"技术部\", \"experience\": \"\", \"job_status\": 2, \"updated_at\": \"2026-07-26 02:30:20\", \"view_count\": 0, \"description\": \"desc\", \"is_hot_label\": \"急聘\", \"requirements\": \"req\", \"salary_range\": \"20k-30k\", \"job_status_label\": \"发布中\"}', 1, '', '127.0.0.1', 'Symfony', 'http://localhost:8000', 'BossJobService@create', '2026-07-26 02:30:20.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669076, 0, '', 'boss_job', 'boss_job_deleted', 'DELETE', 920733863004423259, '测试职位', '{\"id\": \"920733863004423259\", \"is_hot\": 0, \"benefits\": \"五险一金\", \"job_sort\": 0, \"education\": \"\", \"expire_at\": null, \"job_title\": \"测试职位\", \"workplace\": \"深圳\", \"created_at\": \"2026-07-26 02:30:20\", \"department\": \"技术部\", \"experience\": \"\", \"job_status\": 2, \"updated_at\": \"2026-07-26 02:30:20\", \"view_count\": 0, \"description\": \"desc\", \"is_hot_label\": \"否\", \"requirements\": \"req\", \"salary_range\": \"20k-30k\", \"job_status_label\": \"发布中\"}', NULL, 1, '', '127.0.0.1', 'Symfony', 'http://localhost:8000', 'BossJobService@delete', '2026-07-26 02:30:20.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669077, 0, '', 'friend_link', 'friend_link_created', 'INSERT', 920733863055413256, '测试链接', NULL, '{\"id\": \"920733863055413256\", \"link_url\": \"https://example.com\", \"link_desc\": \"demo\", \"link_logo\": \"\", \"link_name\": \"测试链接\", \"link_sort\": 99, \"created_at\": \"2026-07-26 02:33:06\", \"updated_at\": \"2026-07-26 02:33:06\", \"link_status\": 1, \"link_status_label\": \"启用\"}', 1, '', '127.0.0.1', 'Symfony', 'http://localhost:8000', 'FriendLinkService@create', '2026-07-26 02:33:06.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669078, 0, '', 'friend_link', 'friend_link_deleted', 'DELETE', 920733863055413256, '测试链接', '{\"id\": \"920733863055413256\", \"link_url\": \"https://example.com\", \"link_desc\": \"demo\", \"link_logo\": \"\", \"link_name\": \"测试链接\", \"link_sort\": 99, \"created_at\": \"2026-07-26 02:33:06\", \"updated_at\": \"2026-07-26 02:33:06\", \"link_status\": 1, \"link_status_label\": \"启用\"}', NULL, 1, '', '127.0.0.1', 'Symfony', 'http://localhost:8000', 'FriendLinkService@delete', '2026-07-26 02:33:06.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669079, 0, '', 'site_config', 'site_config_batch_updated', 'UPDATE', 0, 'batch_1', NULL, '{\"keys\": [\"site_name\"], \"count\": 1}', 1, '', '127.0.0.1', 'Symfony', 'http://localhost:8000', 'SiteConfigService@batchUpdateValues', '2026-07-26 02:33:06.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669080, 0, '', 'ad_slot', 'ad_slot_created', 'INSERT', 920733863755423258, '临时测试位', NULL, '{\"id\": \"920733863755423258\", \"width\": 200, \"height\": 100, \"is_system\": 0, \"max_items\": 1, \"slot_code\": \"tmp_test_1785033438\", \"slot_name\": \"临时测试位\", \"created_at\": \"2026-07-26 02:37:18\", \"updated_at\": \"2026-07-26 02:37:18\", \"description\": \"\", \"slot_status\": 1, \"slot_status_label\": \"启用\"}', 1, '', '127.0.0.1', 'Symfony', 'http://localhost:8000', 'AdSlotService@create', '2026-07-26 02:37:18.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669081, 0, '', 'ad_position', 'ad_position_created', 'INSERT', 920733863055423259, '临时测试广告', NULL, '{\"id\": \"920733863055423259\", \"sort\": 0, \"app_id\": \"\", \"budget\": null, \"status\": 1, \"ad_title\": \"临时测试广告\", \"app_path\": \"\", \"end_time\": \"2026-08-26 00:00:00\", \"link_url\": \"\", \"platform\": 1, \"subtitle\": \"\", \"weekdays\": [], \"bid_price\": null, \"cost_type\": 1, \"cover_url\": \"\", \"link_type\": 1, \"slot_name\": \"临时测试位\", \"video_url\": \"\", \"click_rate\": \"0.0000\", \"created_at\": \"2026-07-26 02:37:18\", \"created_by\": \"0\", \"start_time\": \"2026-07-26 00:00:00\", \"time_slots\": [], \"updated_at\": \"2026-07-26 02:37:18\", \"click_count\": 0, \"cover_thumb\": \"\", \"daily_stats\": null, \"device_type\": 1, \"link_params\": null, \"reviewed_at\": null, \"reviewer_id\": \"0\", \"audit_status\": 0, \"cover_mobile\": \"\", \"status_label\": \"草稿\", \"position_code\": \"tmp_test_1785033438\", \"reject_reason\": \"\", \"target_region\": null, \"platform_label\": \"全部\", \"show_time_type\": 0, \"cost_type_label\": \"CPM\", \"link_type_label\": \"站内链接\", \"impression_count\": 0, \"target_user_type\": 0, \"daily_click_limit\": 0, \"device_type_label\": \"全部\", \"display_frequency\": 1, \"audit_status_label\": \"未提交\", \"show_time_type_label\": \"全天\", \"target_user_group_ids\": [], \"daily_impression_limit\": 0, \"target_user_type_label\": \"全部用户\", \"display_frequency_label\": \"每人每天1次\"}', 1, '', '127.0.0.1', 'Symfony', 'http://localhost:8000', 'AdPositionService@create', '2026-07-26 02:37:18.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669082, 0, '', 'ad_position', 'ad_position_deleted', 'DELETE', 920733863055423259, '临时测试广告', '{\"id\": \"920733863055423259\", \"sort\": 0, \"app_id\": \"\", \"budget\": null, \"status\": 3, \"ad_title\": \"临时测试广告\", \"app_path\": \"\", \"end_time\": \"2026-08-26 00:00:00\", \"link_url\": \"\", \"platform\": 1, \"subtitle\": \"\", \"weekdays\": [], \"bid_price\": null, \"cost_type\": 1, \"cover_url\": \"\", \"link_type\": 1, \"slot_name\": \"临时测试位\", \"video_url\": \"\", \"click_rate\": \"0.0000\", \"created_at\": \"2026-07-26 02:37:18\", \"created_by\": \"0\", \"start_time\": \"2026-07-26 00:00:00\", \"time_slots\": [], \"updated_at\": \"2026-07-26 02:37:18\", \"click_count\": 0, \"cover_thumb\": \"\", \"daily_stats\": null, \"device_type\": 1, \"link_params\": null, \"reviewed_at\": \"2026-07-26 02:37:18\", \"reviewer_id\": \"0\", \"audit_status\": 2, \"cover_mobile\": \"\", \"status_label\": \"审核通过\", \"position_code\": \"tmp_test_1785033438\", \"reject_reason\": \"\", \"target_region\": null, \"platform_label\": \"全部\", \"show_time_type\": 0, \"cost_type_label\": \"CPM\", \"link_type_label\": \"站内链接\", \"impression_count\": 0, \"target_user_type\": 0, \"daily_click_limit\": 0, \"device_type_label\": \"全部\", \"display_frequency\": 1, \"audit_status_label\": \"审核通过\", \"show_time_type_label\": \"全天\", \"target_user_group_ids\": [], \"daily_impression_limit\": 0, \"target_user_type_label\": \"全部用户\", \"display_frequency_label\": \"每人每天1次\"}', NULL, 1, '', '127.0.0.1', 'Symfony', 'http://localhost:8000', 'AdPositionService@delete', '2026-07-26 02:37:18.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669083, 0, '', 'ad_slot', 'ad_slot_deleted', 'DELETE', 920733863755423258, '临时测试位', '{\"id\": \"920733863755423258\", \"width\": 200, \"height\": 100, \"is_system\": 0, \"max_items\": 1, \"slot_code\": \"tmp_test_1785033438\", \"slot_name\": \"临时测试位\", \"created_at\": \"2026-07-26 02:37:18\", \"updated_at\": \"2026-07-26 02:37:18\", \"description\": \"\", \"slot_status\": 1, \"slot_status_label\": \"启用\"}', NULL, 1, '', '127.0.0.1', 'Symfony', 'http://localhost:8000', 'AdSlotService@delete', '2026-07-26 02:37:18.000000');
INSERT INTO `operation_log` (`id`, `operator_id`, `operator_name`, `biz_type`, `activity_type`, `action`, `biz_id`, `biz_label`, `old_value`, `new_value`, `operator_status`, `error_msg`, `client_ip`, `user_agent`, `request_url`, `method_fun`, `created_at`) VALUES (935126090643669084, 0, '', 'feedback', 'feedback_created', 'INSERT', 920733863054423256, '应聘：PHP高级开发工程师', NULL, '{\"id\": \"920733863054423256\", \"ip\": \"127.0.0.1\", \"fb_name\": \"郭靖\", \"fb_email\": \"\", \"fb_phone\": \"130266611119\", \"fb_title\": \"应聘：PHP高级开发工程师\", \"fb_status\": 0, \"created_at\": \"2026-07-26 14:06:48\", \"fb_company\": \"赛格科技\", \"fb_content\": \"您好，我对「PHP高级开发工程师」（技术研发中心 / 深圳南山区科技园）职位感兴趣，期待沟通。\", \"replied_at\": null, \"updated_at\": \"2026-07-26 14:06:48\", \"reply_content\": null, \"fb_status_label\": \"未处理\"}', 1, '', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'http://127.0.0.1:8000/frontend/api/feedbacks', 'FeedbackService@create', '2026-07-26 14:06:48.000000');
COMMIT;

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for product
-- ----------------------------
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `auto_code` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '系统产生编码SP000001自增',
  `product_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品名称',
  `product_model` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品型号',
  `category_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '商品分类ID',
  `brand_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '品牌ID',
  `material_quality` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '材质',
  `filling` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '填充',
  `short_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '商品简短描述',
  `main_image_url` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '主图URL',
  `product_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态 0=已下架 1=已上架',
  `sort_order` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` datetime(6) DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `deleted_at` datetime(6) DEFAULT NULL,
  `deleted_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `product_category_id_index` (`category_id`) USING BTREE,
  KEY `product_brand_id_index` (`brand_id`) USING BTREE,
  KEY `product_auto_code_index` (`auto_code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733863000000001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of product
-- ----------------------------
BEGIN;
INSERT INTO `product` (`id`, `auto_code`, `product_name`, `product_model`, `category_id`, `brand_id`, `material_quality`, `filling`, `short_desc`, `main_image_url`, `product_status`, `sort_order`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863000000000, 'SP000001', '测试的', 'SFF234324234', 920733862755420000, 920733862755000000, '木头的', '海绵的', '完成产品主表，产品图片暂时就用本地存储，在网站创建文件夹目录存储\n完成产品sku模块。', '/uploads/products/2026/07/01kycdx3a2t5sanh1vjf33jjzc.png', 1, 0, '2026-07-25 10:42:43.000000', 934035802554576897, '2026-07-25 10:42:43.000000', 934035802554576897, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for product_brand
-- ----------------------------
DROP TABLE IF EXISTS `product_brand`;
CREATE TABLE `product_brand` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `brand_code` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '系统产生编码BR000001自增',
  `brand_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '品牌名称',
  `alias` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '英文别名（可选）',
  `is_system` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否系统预设 1=系统预设 0=自定义',
  `is_show` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态 0=隐藏 1=显示',
  `sort_order` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `brand_remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` datetime(6) DEFAULT NULL COMMENT '创建时间',
  `created_by` bigint unsigned DEFAULT NULL COMMENT '创建人',
  `updated_at` datetime(6) DEFAULT NULL COMMENT '更新时间',
  `updated_by` bigint unsigned DEFAULT NULL COMMENT '更新人',
  `deleted_at` datetime(6) DEFAULT NULL COMMENT '删除时间',
  `deleted_by` bigint unsigned DEFAULT NULL COMMENT '删除人',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733862755000005 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of product_brand
-- ----------------------------
BEGIN;
INSERT INTO `product_brand` (`id`, `brand_code`, `brand_name`, `alias`, `is_system`, `is_show`, `sort_order`, `brand_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755000000, 'BR000001', '东风日产', 'Nissan', 1, 1, 100, '系统预设品牌', '2026-07-25 10:29:42.000000', NULL, '2026-07-26 01:33:47.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_brand` (`id`, `brand_code`, `brand_name`, `alias`, `is_system`, `is_show`, `sort_order`, `brand_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755000001, 'BR000002', '苹果Apple', 'Apple', 0, 1, 90, '', '2026-07-25 10:29:42.000000', NULL, '2026-07-26 01:33:38.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_brand` (`id`, `brand_code`, `brand_name`, `alias`, `is_system`, `is_show`, `sort_order`, `brand_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755000002, 'BR000003', '华为', 'Huawei', 0, 1, 80, '', '2026-07-26 01:34:05.000000', 934035802554576897, '2026-07-26 01:34:21.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_brand` (`id`, `brand_code`, `brand_name`, `alias`, `is_system`, `is_show`, `sort_order`, `brand_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755000003, 'BR000004', '兔宝宝', 'Rabbit', 0, 1, 70, '', '2026-07-26 01:34:34.000000', 934035802554576897, '2026-07-26 01:34:34.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_brand` (`id`, `brand_code`, `brand_name`, `alias`, `is_system`, `is_show`, `sort_order`, `brand_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755000004, 'BR000005', 'cesd', 'sdd', 0, 1, 0, '', '2026-07-26 02:08:16.000000', 934035802554576897, '2026-07-26 02:08:36.000000', 934035802554576897, '2026-07-26 02:08:36.000000', 934035802554576897);
COMMIT;

-- ----------------------------
-- Table structure for product_category
-- ----------------------------
DROP TABLE IF EXISTS `product_category`;
CREATE TABLE `product_category` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `category_code` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '系统产生编码FL000001自增',
  `category_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `parent_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '父级分类ID 0是一级分类',
  `level` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '级别 1=一级 2=二级 3=三级',
  `product_count` int unsigned NOT NULL DEFAULT '0' COMMENT '商品数量 冗余',
  `unit` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '数量单位',
  `cat_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态 0=隐藏 1=显示',
  `sort_order` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `cat_remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` datetime(6) DEFAULT NULL COMMENT '创建时间',
  `created_by` bigint unsigned DEFAULT NULL COMMENT '创建人',
  `updated_at` datetime(6) DEFAULT NULL COMMENT '更新时间',
  `updated_by` bigint unsigned DEFAULT NULL COMMENT '更新人',
  `deleted_at` datetime(6) DEFAULT NULL COMMENT '删除时间',
  `deleted_by` bigint unsigned DEFAULT NULL COMMENT '删除人',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `category_parent_id_index` (`parent_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733862755420006 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of product_category
-- ----------------------------
BEGIN;
INSERT INTO `product_category` (`id`, `category_code`, `category_name`, `parent_id`, `level`, `product_count`, `unit`, `cat_status`, `sort_order`, `cat_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755420000, 'FL000001', '手机', 0, 1, 1, '台', 1, 100, '', '2026-07-25 10:29:42.000000', NULL, '2026-07-26 01:34:57.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_category` (`id`, `category_code`, `category_name`, `parent_id`, `level`, `product_count`, `unit`, `cat_status`, `sort_order`, `cat_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755420001, 'FL000002', '苹果手机', 920733862755420000, 2, 0, '台', 1, 20, '', '2026-07-25 10:29:42.000000', NULL, '2026-07-26 01:35:10.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_category` (`id`, `category_code`, `category_name`, `parent_id`, `level`, `product_count`, `unit`, `cat_status`, `sort_order`, `cat_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755420002, 'FL000003', '安卓手机', 920733862755420000, 2, 0, '台', 1, 10, '', '2026-07-25 10:29:42.000000', NULL, '2026-07-26 01:35:19.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_category` (`id`, `category_code`, `category_name`, `parent_id`, `level`, `product_count`, `unit`, `cat_status`, `sort_order`, `cat_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755420003, 'FL000004', '汽车', 0, 1, 0, '辆', 1, 50, '', '2026-07-26 01:35:34.000000', 934035802554576897, '2026-07-26 01:35:34.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_category` (`id`, `category_code`, `category_name`, `parent_id`, `level`, `product_count`, `unit`, `cat_status`, `sort_order`, `cat_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755420004, 'FL000005', '塞力斯', 920733862755420003, 2, 0, '辆', 1, 51, '', '2026-07-26 01:36:14.000000', 934035802554576897, '2026-07-26 01:36:14.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_category` (`id`, `category_code`, `category_name`, `parent_id`, `level`, `product_count`, `unit`, `cat_status`, `sort_order`, `cat_remark`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755420005, 'FL000006', '东风日产', 920733862755420003, 2, 0, '辆', 1, 52, '', '2026-07-26 01:36:39.000000', 934035802554576897, '2026-07-26 01:36:49.000000', 934035802554576897, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for product_media
-- ----------------------------
DROP TABLE IF EXISTS `product_media`;
CREATE TABLE `product_media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `product_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `media_type` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '类型 1=主图 2=详情图 3=视频 4=资质文件 5=其他附件',
  `file_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '文件URL',
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '原始文件名',
  `file_key` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '存储键/路径',
  `storage_provider` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local' COMMENT '存储提供方',
  `extension` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件拓展名',
  `file_size` bigint unsigned NOT NULL DEFAULT '0' COMMENT '字节大小',
  `file_type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'MimeType',
  `sort_order` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` datetime(6) DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `deleted_at` datetime(6) DEFAULT NULL,
  `deleted_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `media_product_id_index` (`product_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733863100000002 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of product_media
-- ----------------------------
BEGIN;
INSERT INTO `product_media` (`id`, `product_id`, `media_type`, `file_url`, `file_name`, `file_key`, `storage_provider`, `extension`, `file_size`, `file_type`, `sort_order`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863100000000, 920733863000000000, 1, '/uploads/products/2026/07/01kycdx3a2t5sanh1vjf33jjzc.png', 'gywm_logo.png', 'uploads/products/2026/07/01kycdx3a2t5sanh1vjf33jjzc.png', 'local', 'png', 4418, 'image/png', 0, '2026-07-25 10:42:45.000000', 934035802554576897, '2026-07-25 10:42:45.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_media` (`id`, `product_id`, `media_type`, `file_url`, `file_name`, `file_key`, `storage_provider`, `extension`, `file_size`, `file_type`, `sort_order`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863100000001, 920733863000000000, 2, '/uploads/products/2026/07/01kycdx90zj2vpppkafdd0fkaa.png', '设计方案.png', 'uploads/products/2026/07/01kycdx90zj2vpppkafdd0fkaa.png', 'local', 'png', 1581757, 'image/png', 1, '2026-07-25 10:42:45.000000', 934035802554576897, '2026-07-25 10:42:45.000000', 934035802554576897, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for product_sku
-- ----------------------------
DROP TABLE IF EXISTS `product_sku`;
CREATE TABLE `product_sku` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `product_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `sku_code` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'SKU编码',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '销售价',
  `market_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '划线价/市场价',
  `cost_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `stock_num` int unsigned NOT NULL DEFAULT '0' COMMENT '库存数量',
  `weight` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '重量(KG)',
  `volume` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT '体积(m³)',
  `sale_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '销售状态 0下架 1上架',
  `sort_order` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` datetime(6) DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `deleted_at` datetime(6) DEFAULT NULL,
  `deleted_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `sku_code_unique` (`sku_code`) USING BTREE,
  KEY `sku_product_id_index` (`product_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733863200000006 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of product_sku
-- ----------------------------
BEGIN;
INSERT INTO `product_sku` (`id`, `product_id`, `sku_code`, `price`, `market_price`, `cost_price`, `stock_num`, `weight`, `volume`, `sale_status`, `sort_order`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863200000000, 920733863000000000, 'SKU000001', 10.00, 20.00, 0.00, 0, 10.00, 0.1000, 1, 0, '2026-07-25 10:42:43.000000', 934035802554576897, '2026-07-25 10:42:43.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku` (`id`, `product_id`, `sku_code`, `price`, `market_price`, `cost_price`, `stock_num`, `weight`, `volume`, `sale_status`, `sort_order`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863200000001, 920733863000000000, 'SKU000002', 20.00, 30.00, 0.00, 0, 20.00, 0.1000, 1, 1, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku` (`id`, `product_id`, `sku_code`, `price`, `market_price`, `cost_price`, `stock_num`, `weight`, `volume`, `sale_status`, `sort_order`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863200000002, 920733863000000000, 'SKU000003', 30.00, 40.00, 0.00, 0, 30.00, 1.0000, 1, 2, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku` (`id`, `product_id`, `sku_code`, `price`, `market_price`, `cost_price`, `stock_num`, `weight`, `volume`, `sale_status`, `sort_order`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863200000003, 920733863000000000, 'SKU000004', 40.00, 50.00, 0.00, 0, 40.00, 0.1000, 1, 3, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku` (`id`, `product_id`, `sku_code`, `price`, `market_price`, `cost_price`, `stock_num`, `weight`, `volume`, `sale_status`, `sort_order`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863200000004, 920733863000000000, 'SKU000005', 50.00, 60.00, 0.00, 0, 50.00, 0.1000, 1, 4, '2026-07-25 10:42:45.000000', 934035802554576897, '2026-07-25 10:42:45.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku` (`id`, `product_id`, `sku_code`, `price`, `market_price`, `cost_price`, `stock_num`, `weight`, `volume`, `sale_status`, `sort_order`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863200000005, 920733863000000000, 'SKU000006', 60.00, 70.00, 0.00, 0, 60.00, 0.0100, 1, 5, '2026-07-25 10:42:45.000000', 934035802554576897, '2026-07-25 10:42:45.000000', 934035802554576897, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for product_sku_spec_value
-- ----------------------------
DROP TABLE IF EXISTS `product_sku_spec_value`;
CREATE TABLE `product_sku_spec_value` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `sku_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '关联SKU表ID',
  `spec_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '关联规格维度ID',
  `spec_value_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '关联规格值ID',
  `created_at` datetime(6) DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `deleted_at` datetime(6) DEFAULT NULL,
  `deleted_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `sku_spec_value_unique` (`sku_id`,`spec_id`,`spec_value_id`) USING BTREE,
  KEY `sku_spec_sku_id_index` (`sku_id`) USING BTREE,
  KEY `sku_spec_value_id_index` (`spec_value_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733863300000012 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of product_sku_spec_value
-- ----------------------------
BEGIN;
INSERT INTO `product_sku_spec_value` (`id`, `sku_id`, `spec_id`, `spec_value_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863300000000, 920733863200000000, 920733862755400000, 920733862755320000, '2026-07-25 10:42:43.000000', 934035802554576897, '2026-07-25 10:42:43.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` (`id`, `sku_id`, `spec_id`, `spec_value_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863300000001, 920733863200000000, 920733862755400001, 920733862755320003, '2026-07-25 10:42:43.000000', 934035802554576897, '2026-07-25 10:42:43.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` (`id`, `sku_id`, `spec_id`, `spec_value_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863300000002, 920733863200000001, 920733862755400000, 920733862755320000, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` (`id`, `sku_id`, `spec_id`, `spec_value_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863300000003, 920733863200000001, 920733862755400001, 920733862755320004, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` (`id`, `sku_id`, `spec_id`, `spec_value_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863300000004, 920733863200000002, 920733862755400000, 920733862755320001, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` (`id`, `sku_id`, `spec_id`, `spec_value_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863300000005, 920733863200000002, 920733862755400001, 920733862755320003, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` (`id`, `sku_id`, `spec_id`, `spec_value_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863300000006, 920733863200000003, 920733862755400000, 920733862755320001, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` (`id`, `sku_id`, `spec_id`, `spec_value_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863300000007, 920733863200000003, 920733862755400001, 920733862755320004, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` (`id`, `sku_id`, `spec_id`, `spec_value_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863300000008, 920733863200000004, 920733862755400000, 920733862755320002, '2026-07-25 10:42:45.000000', 934035802554576897, '2026-07-25 10:42:45.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` (`id`, `sku_id`, `spec_id`, `spec_value_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863300000009, 920733863200000004, 920733862755400001, 920733862755320003, '2026-07-25 10:42:45.000000', 934035802554576897, '2026-07-25 10:42:45.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` (`id`, `sku_id`, `spec_id`, `spec_value_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863300000010, 920733863200000005, 920733862755400000, 920733862755320002, '2026-07-25 10:42:45.000000', 934035802554576897, '2026-07-25 10:42:45.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` (`id`, `sku_id`, `spec_id`, `spec_value_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733863300000011, 920733863200000005, 920733862755400001, 920733862755320004, '2026-07-25 10:42:45.000000', 934035802554576897, '2026-07-25 10:42:45.000000', 934035802554576897, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for product_specification
-- ----------------------------
DROP TABLE IF EXISTS `product_specification`;
CREATE TABLE `product_specification` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `spec_code` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '系统产生编码GL000001自增',
  `spec_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '规格名称',
  `spec_remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `spec_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态 0=隐藏 1=显示',
  `sort_order` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` datetime(6) DEFAULT NULL COMMENT '创建时间',
  `created_by` bigint unsigned DEFAULT NULL COMMENT '创建人',
  `updated_at` datetime(6) DEFAULT NULL COMMENT '更新时间',
  `updated_by` bigint unsigned DEFAULT NULL COMMENT '更新人',
  `deleted_at` datetime(6) DEFAULT NULL COMMENT '删除时间',
  `deleted_by` bigint unsigned DEFAULT NULL COMMENT '删除人',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733862755400005 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of product_specification
-- ----------------------------
BEGIN;
INSERT INTO `product_specification` (`id`, `spec_code`, `spec_name`, `spec_remark`, `spec_status`, `sort_order`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755400000, 'GL000001', '颜色', '', 1, 100, '2026-07-25 10:29:43.000000', NULL, '2026-07-25 10:29:43.000000', NULL, NULL, NULL);
INSERT INTO `product_specification` (`id`, `spec_code`, `spec_name`, `spec_remark`, `spec_status`, `sort_order`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755400001, 'GL000002', '材质', '', 1, 90, '2026-07-25 10:29:43.000000', NULL, '2026-07-25 10:29:43.000000', NULL, NULL, NULL);
INSERT INTO `product_specification` (`id`, `spec_code`, `spec_name`, `spec_remark`, `spec_status`, `sort_order`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755400002, 'GL000003', '内存容量', '', 1, 80, '2026-07-26 01:37:15.000000', 934035802554576897, '2026-07-26 01:37:15.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_specification` (`id`, `spec_code`, `spec_name`, `spec_remark`, `spec_status`, `sort_order`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755400003, 'GL000004', '硬盘容量', '', 1, 20, '2026-07-26 01:38:04.000000', 934035802554576897, '2026-07-26 01:38:04.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_specification` (`id`, `spec_code`, `spec_name`, `spec_remark`, `spec_status`, `sort_order`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755400004, 'GL000005', '配置', '', 1, 0, '2026-07-26 01:39:18.000000', 934035802554576897, '2026-07-26 01:39:18.000000', 934035802554576897, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for product_specification_value
-- ----------------------------
DROP TABLE IF EXISTS `product_specification_value`;
CREATE TABLE `product_specification_value` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `spec_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '规格ID',
  `value_code` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '系统产生编码GV000001自增',
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '规格值',
  `sort_order` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `value_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态 0=隐藏 1=显示',
  `created_at` datetime(6) DEFAULT NULL COMMENT '创建时间',
  `created_by` bigint unsigned DEFAULT NULL COMMENT '创建人',
  `updated_at` datetime(6) DEFAULT NULL COMMENT '更新时间',
  `updated_by` bigint unsigned DEFAULT NULL COMMENT '更新人',
  `deleted_at` datetime(6) DEFAULT NULL COMMENT '删除时间',
  `deleted_by` bigint unsigned DEFAULT NULL COMMENT '删除人',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `spec_value_spec_id_index` (`spec_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733862755320012 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of product_specification_value
-- ----------------------------
BEGIN;
INSERT INTO `product_specification_value` (`id`, `spec_id`, `value_code`, `value`, `sort_order`, `value_status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755320000, 920733862755400000, 'GV000001', '红色', 30, 1, '2026-07-25 10:29:43.000000', NULL, '2026-07-25 10:29:43.000000', NULL, NULL, NULL);
INSERT INTO `product_specification_value` (`id`, `spec_id`, `value_code`, `value`, `sort_order`, `value_status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755320001, 920733862755400000, 'GV000002', '黑色', 20, 1, '2026-07-25 10:29:43.000000', NULL, '2026-07-25 10:29:43.000000', NULL, NULL, NULL);
INSERT INTO `product_specification_value` (`id`, `spec_id`, `value_code`, `value`, `sort_order`, `value_status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755320002, 920733862755400000, 'GV000003', '白色', 10, 1, '2026-07-25 10:29:43.000000', NULL, '2026-07-25 10:29:43.000000', NULL, NULL, NULL);
INSERT INTO `product_specification_value` (`id`, `spec_id`, `value_code`, `value`, `sort_order`, `value_status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755320003, 920733862755400001, 'GV000004', '实木', 20, 1, '2026-07-25 10:29:43.000000', NULL, '2026-07-25 10:29:43.000000', NULL, NULL, NULL);
INSERT INTO `product_specification_value` (`id`, `spec_id`, `value_code`, `value`, `sort_order`, `value_status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755320004, 920733862755400001, 'GV000005', '布艺', 10, 1, '2026-07-25 10:29:43.000000', NULL, '2026-07-25 10:29:43.000000', NULL, NULL, NULL);
INSERT INTO `product_specification_value` (`id`, `spec_id`, `value_code`, `value`, `sort_order`, `value_status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755320005, 920733862755400002, 'GV000006', '12G', 1, 1, '2026-07-26 01:37:28.000000', 934035802554576897, '2026-07-26 01:37:43.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_specification_value` (`id`, `spec_id`, `value_code`, `value`, `sort_order`, `value_status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755320006, 920733862755400002, 'GV000007', '8G', 2, 1, '2026-07-26 01:37:38.000000', 934035802554576897, '2026-07-26 01:37:47.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_specification_value` (`id`, `spec_id`, `value_code`, `value`, `sort_order`, `value_status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755320007, 920733862755400003, 'GV000008', '128G', 10, 1, '2026-07-26 01:38:17.000000', 934035802554576897, '2026-07-26 01:38:17.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_specification_value` (`id`, `spec_id`, `value_code`, `value`, `sort_order`, `value_status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755320008, 920733862755400003, 'GV000009', '256G', 11, 1, '2026-07-26 01:38:25.000000', 934035802554576897, '2026-07-26 01:38:39.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_specification_value` (`id`, `spec_id`, `value_code`, `value`, `sort_order`, `value_status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755320009, 920733862755400003, 'GV000010', '512G', 12, 1, '2026-07-26 01:38:30.000000', 934035802554576897, '2026-07-26 01:38:40.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_specification_value` (`id`, `spec_id`, `value_code`, `value`, `sort_order`, `value_status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755320010, 920733862755400003, 'GV000011', '1T', 13, 1, '2026-07-26 01:38:36.000000', 934035802554576897, '2026-07-26 01:38:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_specification_value` (`id`, `spec_id`, `value_code`, `value`, `sort_order`, `value_status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES (920733862755320011, 920733862755400004, 'GV000012', '旗舰版', 11, 1, '2026-07-26 01:39:32.000000', 934035802554576897, '2026-07-26 01:39:32.000000', 934035802554576897, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `sessions_user_id_index` (`user_id`) USING BTREE,
  KEY `sessions_last_activity_index` (`last_activity`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of sessions
-- ----------------------------
BEGIN;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('6sJQX5pfEYMA83ohczwhFVigbZDhWVaZCPdaL06c', NULL, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiI5RTREWFc2NDRrenVMbEVDYmlvMnFLZDhJQWFUcTZYa09maWNWT3J6IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvYmFja2VuZFwvbG9naW4iLCJyb3V0ZSI6ImJhY2tlbmQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl9iYWNrZW5kXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjkyMDczMzg2MDc1NTQyMzAwMX0=', 1785030797);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('hh0MMQMiMz2LaPhGIOBpHRbMaa9QKf2jxFj9f8qF', NULL, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJCM0NDUzhNNnNpUlVoNjFCMlM3czdONW5kblpBeW9DSzZpYUpER1B5IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvYmFja2VuZFwvbG9naW4iLCJyb3V0ZSI6ImJhY2tlbmQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1785030797);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('j3ZfcghlvXOxbzNjKLmmXz6Fvw1MnvHfbvIY10en', NULL, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJlRWlra2FCWTRjeVo5aDNLNUU3cmJiOWlJeVpwYTZKV0EwcmlIVHJKIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvYmFja2VuZFwvbG9naW4iLCJyb3V0ZSI6ImJhY2tlbmQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl9iYWNrZW5kXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjkzNDAzNTgwMjU1NDU3Njg5N30=', 1785030797);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('RtFa7yzX4HSYkRCLEr9fAVFe4mS7RavfakTzteJx', NULL, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJPZDN3djFxYmNOeTFwbTJ2Y3dJeFkwdHBvS1VwUEZ4cWQxT0FkalpqIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvYmFja2VuZFwvbG9naW4iLCJyb3V0ZSI6ImJhY2tlbmQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1785031494);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('sCVfrcGw9G4Rd8uKCcIu0NGKpZxenXwa9BRrpwo0', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJaRGF1SUVHWWpoWWpFeWI3U1E3TXVDMDc3N0pmMDEyOFFsZDMxRm5iIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1785075143);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('taO7F9mpgSh6HauMUbQFwamtb1mhmX5DsvUmAuRs', NULL, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJMbmRXODJaTUR0ZXg0bkNYdThvQjRINWZhazBPb1VRZjNmM3R1UmxHIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0XC9iYWNrZW5kXC9hcGlcL2FkLXNsb3RzXC9vcHRpb25zIiwicm91dGUiOm51bGx9fQ==', 1785033428);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('vFJZ9oSmRJvh9MYDUE615ksmp33by35tKZhR6liJ', NULL, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJzNlVvTmNkS21OdkVEZlp5ejh2eFJaakg3bE1lQ2pOZ2IwYjNmYWltIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvYmFja2VuZFwvbG9naW4iLCJyb3V0ZSI6ImJhY2tlbmQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl9iYWNrZW5kXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjkzNDAzNTgwMjU1NDU3Njg5N30=', 1785030730);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('VU8XSfhFYUAlw7Lo2hvFPIokDMHAJjEI8bArHLkI', NULL, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJGNWlIdlBPSHdHT1NNVWhYaWZFQ1dZcmpCNHNTc0EzcUdqR0U4WHkyIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvYmFja2VuZFwvbG9naW4iLCJyb3V0ZSI6ImJhY2tlbmQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl9iYWNrZW5kXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjkzNDAzNTgwMjU1NDU3Njg5N30=', 1785030797);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('x9iATqqYi7cXJF8iiqWPMhtM7j16XaVImilRb53X', NULL, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiJBRVllVk42MXFxWnVIOUF3VEdoN3l2eHhIWkcwN0NVNjc1WDdwMWZlIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdFwvYmFja2VuZFwvbG9naW4iLCJyb3V0ZSI6ImJhY2tlbmQuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl9iYWNrZW5kXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjkyMDczMzg2MDc1NTQyMzAwMn0=', 1785030797);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('zSVt7cqyXS9xO6nQLD6DWJmuUu4SyvgAFxQluf9d', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJZSlUybFRQTkxJUHB5M2VIeVFmSHlINWpSeGp0TEdLb25GelNDc3FRIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl9iYWNrZW5kXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjkzNDAzNTgwMjU1NDU3Njg5NywiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9iYWNrZW5kXC9oclwvcG9zdHMiLCJyb3V0ZSI6ImJhY2tlbmQuaW5kZXgifX0=', 1785038834);
COMMIT;

-- ----------------------------
-- Table structure for site_configs
-- ----------------------------
DROP TABLE IF EXISTS `site_configs`;
CREATE TABLE `site_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conf_group` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'basic' COMMENT '配置分组：basic, seo, contact, social',
  `conf_key` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '配置键名',
  `conf_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '配置值',
  `conf_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '配置说明',
  `input_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'text' COMMENT '输入类型：text, textarea, image, file, json',
  `conf_sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733863044423256 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci ROW_FORMAT=DYNAMIC COMMENT='站点配置表';

-- ----------------------------
-- Records of site_configs
-- ----------------------------
BEGIN;
INSERT INTO `site_configs` (`id`, `conf_group`, `conf_key`, `conf_value`, `conf_desc`, `input_type`, `conf_sort`, `created_at`, `updated_at`) VALUES (920733863044423246, 'basic', 'site_name', '名扬科技', '站点名称', 'text', 10, '2026-07-23 07:30:43', '2026-07-26 03:07:28');
INSERT INTO `site_configs` (`id`, `conf_group`, `conf_key`, `conf_value`, `conf_desc`, `input_type`, `conf_sort`, `created_at`, `updated_at`) VALUES (920733863044423247, 'basic', 'site_title', '深圳市名扬科技 — 企业数字化与智能云服务', '站点标题', 'textarea', 20, '2026-07-23 07:30:43', '2026-07-26 03:07:28');
INSERT INTO `site_configs` (`id`, `conf_group`, `conf_key`, `conf_value`, `conf_desc`, `input_type`, `conf_sort`, `created_at`, `updated_at`) VALUES (920733863044423248, 'basic', 'site_keywords', '名扬科技,深圳软件,数字化转型,企业云,AI平台', '站点关键词', 'textarea', 30, '2026-07-23 07:30:43', '2026-07-26 03:07:28');
INSERT INTO `site_configs` (`id`, `conf_group`, `conf_key`, `conf_value`, `conf_desc`, `input_type`, `conf_sort`, `created_at`, `updated_at`) VALUES (920733863044423249, 'basic', 'site_description', '深圳市名扬科技专注企业数字化建设，提供云平台、智能应用与行业解决方案，助力企业稳健增长。', '站点描述', 'textarea', 40, '2026-07-23 07:30:43', '2026-07-26 03:07:28');
INSERT INTO `site_configs` (`id`, `conf_group`, `conf_key`, `conf_value`, `conf_desc`, `input_type`, `conf_sort`, `created_at`, `updated_at`) VALUES (920733863044423250, 'contact', 'phone', '13026661119', '联系电话', 'text', 10, '2026-07-23 07:30:43', '2026-07-26 22:08:50');
INSERT INTO `site_configs` (`id`, `conf_group`, `conf_key`, `conf_value`, `conf_desc`, `input_type`, `conf_sort`, `created_at`, `updated_at`) VALUES (920733863044423251, 'contact', 'email', 'githup@163.com', '邮箱地址', 'text', 20, '2026-07-23 07:30:43', '2026-07-26 22:08:59');
INSERT INTO `site_configs` (`id`, `conf_group`, `conf_key`, `conf_value`, `conf_desc`, `input_type`, `conf_sort`, `created_at`, `updated_at`) VALUES (920733863044423252, 'contact', 'address', '深圳市龙岗区科技园南区科苑路33号333楼', '公司地址', 'textarea', 30, '2026-07-23 07:30:43', '2026-07-26 22:08:42');
INSERT INTO `site_configs` (`id`, `conf_group`, `conf_key`, `conf_value`, `conf_desc`, `input_type`, `conf_sort`, `created_at`, `updated_at`) VALUES (920733863044423253, 'social', 'wechat', 'itcont', '微信公众号', 'text', 10, '2026-07-23 07:30:43', '2026-07-26 22:09:18');
INSERT INTO `site_configs` (`id`, `conf_group`, `conf_key`, `conf_value`, `conf_desc`, `input_type`, `conf_sort`, `created_at`, `updated_at`) VALUES (920733863044423254, 'social', 'weibo', 'itcont', '微博地址', 'text', 0, '2026-07-23 07:30:43', '2026-07-26 22:09:27');
INSERT INTO `site_configs` (`id`, `conf_group`, `conf_key`, `conf_value`, `conf_desc`, `input_type`, `conf_sort`, `created_at`, `updated_at`) VALUES (920733863044423255, 'basic', 'company_full_name', '深圳市名扬科技有限公司', '公司全称', 'text', 50, '2026-07-26 03:07:28', '2026-07-26 03:07:28');
COMMIT;

-- ----------------------------
-- Table structure for user_account
-- ----------------------------
DROP TABLE IF EXISTS `user_account`;
CREATE TABLE `user_account` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '用户唯一主键ID（雪花ID，不自增，分布式安全）',
  `user_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '账号用户名，唯一，可用于登录',
  `nick_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户昵称',
  `user_mobile` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '手机号，唯一索引，登录首选',
  `user_email` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮箱，唯一索引，找回密码',
  `password_hash` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'BCrypt/Argon2加密密码，禁止明文存储',
  `password_salt` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '自定义盐值（BCrypt自带盐可留空）',
  `user_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '账号状态：0-禁用 1-正常 2-冻结 3-注销',
  `lock_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '冻结/封禁原因（风控、违规、人工封禁）',
  `lock_expire_time` datetime DEFAULT NULL COMMENT '限时冻结到期时间，NULL=永久封禁',
  `last_login_ip` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_region` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'IP归属地',
  `last_login_at` datetime DEFAULT NULL COMMENT '最后登录时间',
  `register_ip` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '注册IP',
  `register_device` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '注册设备标识',
  `register_channel` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web' COMMENT '注册渠道：web/app/mini/ios/android',
  `real_auth_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '实名状态：0未实名 1待审核 2已实名 3实名驳回',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间（软删除记录）',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_username` (`user_name`) USING BTREE,
  UNIQUE KEY `uk_mobile` (`user_mobile`) USING BTREE,
  UNIQUE KEY `uk_email` (`user_email`) USING BTREE,
  KEY `idx_status_auth` (`user_status`,`real_auth_status`) USING BTREE,
  KEY `idx_deleted_time` (`created_at`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=934035802554576899 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='用户账号主表｜登录、安全、状态核心数据';

-- ----------------------------
-- Records of user_account
-- ----------------------------
BEGIN;
INSERT INTO `user_account` (`id`, `user_name`, `nick_name`, `user_mobile`, `user_email`, `password_hash`, `password_salt`, `user_status`, `lock_reason`, `lock_expire_time`, `last_login_ip`, `last_login_region`, `last_login_at`, `register_ip`, `register_device`, `register_channel`, `real_auth_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423001, 'admin123', '', '13800000001', 'admin@company.com', '$2y$12$TMbO5wisUaMV8n977794heEtiFCzwc7zpt1s.k27hsUbBf9HHQ.iG', '', 1, '', NULL, '127.0.0.1', '', '2026-07-26 01:53:17', '192.168.1.1', 'Chrome/Windows', 'web', 2, '2026-01-01 00:00:00', '2026-07-26 09:57:48', NULL);
INSERT INTO `user_account` (`id`, `user_name`, `nick_name`, `user_mobile`, `user_email`, `password_hash`, `password_salt`, `user_status`, `lock_reason`, `lock_expire_time`, `last_login_ip`, `last_login_region`, `last_login_at`, `register_ip`, `register_device`, `register_channel`, `real_auth_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423002, 'super_admin', '第二管理员', '13800000002', 'super@company.com', '$2y$12$TMbO5wisUaMV8n977794heEtiFCzwc7zpt1s.k27hsUbBf9HHQ.iG', '', 1, '', NULL, '127.0.0.1', '', '2026-07-26 01:53:17', '192.168.1.1', 'Chrome/Mac', 'web', 2, '2026-01-02 00:00:00', '2026-07-26 01:57:25', NULL);
INSERT INTO `user_account` (`id`, `user_name`, `nick_name`, `user_mobile`, `user_email`, `password_hash`, `password_salt`, `user_status`, `lock_reason`, `lock_expire_time`, `last_login_ip`, `last_login_region`, `last_login_at`, `register_ip`, `register_device`, `register_channel`, `real_auth_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423003, 'editor_zhang', '', '13800000003', 'zhangwei@company.com', '$2y$12$TMbO5wisUaMV8n977794heEtiFCzwc7zpt1s.k27hsUbBf9HHQ.iG', '', 1, '123456', NULL, '192.168.1.102', '广东广州', '2026-07-23 10:00:00', '192.168.1.2', 'Safari/iPhone', 'app', 2, '2026-02-01 00:00:00', '2026-07-26 09:57:49', NULL);
INSERT INTO `user_account` (`id`, `user_name`, `nick_name`, `user_mobile`, `user_email`, `password_hash`, `password_salt`, `user_status`, `lock_reason`, `lock_expire_time`, `last_login_ip`, `last_login_region`, `last_login_at`, `register_ip`, `register_device`, `register_channel`, `real_auth_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423004, 'ops_li', '', '13800000004', 'liming@company.com', '$2y$12$TMbO5wisUaMV8n977794heEtiFCzwc7zpt1s.k27hsUbBf9HHQ.iG', '', 1, '123456', NULL, '10.0.0.5', '广东深圳', '2026-07-22 16:00:00', '10.0.0.1', 'Chrome/Windows', 'web', 2, '2026-03-01 00:00:00', '2026-07-26 09:57:50', NULL);
INSERT INTO `user_account` (`id`, `user_name`, `nick_name`, `user_mobile`, `user_email`, `password_hash`, `password_salt`, `user_status`, `lock_reason`, `lock_expire_time`, `last_login_ip`, `last_login_region`, `last_login_at`, `register_ip`, `register_device`, `register_channel`, `real_auth_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423005, 'pm_wang', '', '13800000005', 'wangfang@company.com', '$2y$12$TMbO5wisUaMV8n977794heEtiFCzwc7zpt1s.k27hsUbBf9HHQ.iG', '', 1, '123456', NULL, '172.16.0.10', '上海浦东', '2026-07-21 14:00:00', '172.16.0.1', 'Edge/Windows', 'mini', 1, '2026-04-01 00:00:00', '2026-07-26 09:57:51', NULL);
INSERT INTO `user_account` (`id`, `user_name`, `nick_name`, `user_mobile`, `user_email`, `password_hash`, `password_salt`, `user_status`, `lock_reason`, `lock_expire_time`, `last_login_ip`, `last_login_region`, `last_login_at`, `register_ip`, `register_device`, `register_channel`, `real_auth_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423006, 'sales_chen', '', '13800000006', 'chenjun@company.com', '$2y$12$TMbO5wisUaMV8n977794heEtiFCzwc7zpt1s.k27hsUbBf9HHQ.iG', '', 1, '123456', NULL, '192.168.2.15', '北京朝阳', '2026-07-20 11:00:00', '192.168.2.1', 'Chrome/Mac', 'ios', 0, '2026-05-01 00:00:00', '2026-07-26 09:57:51', NULL);
INSERT INTO `user_account` (`id`, `user_name`, `nick_name`, `user_mobile`, `user_email`, `password_hash`, `password_salt`, `user_status`, `lock_reason`, `lock_expire_time`, `last_login_ip`, `last_login_region`, `last_login_at`, `register_ip`, `register_device`, `register_channel`, `real_auth_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423007, 'finance_lin', '', '13800000007', 'linna@company.com', '$2y$12$TMbO5wisUaMV8n977794heEtiFCzwc7zpt1s.k27hsUbBf9HHQ.iG', '', 1, '123456', NULL, '10.0.1.20', '广东深圳', '2026-07-19 09:30:00', '10.0.1.1', 'Firefox/Windows', 'web', 1, '2026-06-01 00:00:00', '2026-07-26 09:57:52', NULL);
INSERT INTO `user_account` (`id`, `user_name`, `nick_name`, `user_mobile`, `user_email`, `password_hash`, `password_salt`, `user_status`, `lock_reason`, `lock_expire_time`, `last_login_ip`, `last_login_region`, `last_login_at`, `register_ip`, `register_device`, `register_channel`, `real_auth_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423008, 'intern_huang', '', '13800000008', 'huangxiao@company.com', '$2y$12$TMbO5wisUaMV8n977794heEtiFCzwc7zpt1s.k27hsUbBf9HHQ.iG', '', 1, '123456', NULL, '192.168.3.30', '广东广州', '2026-07-18 08:00:00', '192.168.3.1', 'Safari/iPhone', 'app', 0, '2026-07-01 00:00:00', '2026-07-26 09:57:53', NULL);
INSERT INTO `user_account` (`id`, `user_name`, `nick_name`, `user_mobile`, `user_email`, `password_hash`, `password_salt`, `user_status`, `lock_reason`, `lock_expire_time`, `last_login_ip`, `last_login_region`, `last_login_at`, `register_ip`, `register_device`, `register_channel`, `real_auth_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423009, 'former_zhao', '', '13800000009', 'zhaolei@company.com', '$2y$12$TMbO5wisUaMV8n977794heEtiFCzwc7zpt1s.k27hsUbBf9HHQ.iG', '', 2, '因违反公司信息安全规定，账号临时冻结', '2026-08-31 23:59:59', '192.168.4.50', '广东深圳', '2026-07-10 15:00:00', '192.168.4.1', 'Chrome/Windows', 'web', 2, '2026-01-15 00:00:00', '2026-07-26 09:57:53', NULL);
INSERT INTO `user_account` (`id`, `user_name`, `nick_name`, `user_mobile`, `user_email`, `password_hash`, `password_salt`, `user_status`, `lock_reason`, `lock_expire_time`, `last_login_ip`, `last_login_region`, `last_login_at`, `register_ip`, `register_device`, `register_channel`, `real_auth_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733860755423010, 'former_lu', '', '13800000010', 'luyang@company.com', '$2y$12$TMbO5wisUaMV8n977794heEtiFCzwc7zpt1s.k27hsUbBf9HHQ.iG', '', 3, '用户主动注销', NULL, '192.168.5.60', '浙江杭州', '2026-06-01 10:00:00', '192.168.5.1', 'Chrome/Mac', 'web', 2, '2025-12-01 00:00:00', '2026-07-26 09:57:54', '2026-06-01 10:00:00');
INSERT INTO `user_account` (`id`, `user_name`, `nick_name`, `user_mobile`, `user_email`, `password_hash`, `password_salt`, `user_status`, `lock_reason`, `lock_expire_time`, `last_login_ip`, `last_login_region`, `last_login_at`, `register_ip`, `register_device`, `register_channel`, `real_auth_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (934035802554576896, 'sunny', '', '13026661119', 'itpeeg@gmail.com', '$2y$12$TMbO5wisUaMV8n977794heEtiFCzwc7zpt1s.k27hsUbBf9HHQ.iG', '', 1, '', NULL, '127.0.0.1', '', '2026-07-26 02:00:17', '', '', 'web', 0, '2026-07-22 02:39:20', '2026-07-26 02:00:17', NULL);
INSERT INTO `user_account` (`id`, `user_name`, `nick_name`, `user_mobile`, `user_email`, `password_hash`, `password_salt`, `user_status`, `lock_reason`, `lock_expire_time`, `last_login_ip`, `last_login_region`, `last_login_at`, `register_ip`, `register_device`, `register_channel`, `real_auth_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (934035802554576897, 'admin', '管理员', '13800000000', 'admin@example.com', '$2y$12$E3kNwXHCQbGfRst1oGznTuuURcVleiDJq2Nmqfla5pMJzlU1IzMSi', '', 1, '', NULL, '127.0.0.1', '', '2026-07-26 02:05:08', '', '', 'web', 2, '2026-07-23 02:21:16', '2026-07-26 02:05:08', NULL);
INSERT INTO `user_account` (`id`, `user_name`, `nick_name`, `user_mobile`, `user_email`, `password_hash`, `password_salt`, `user_status`, `lock_reason`, `lock_expire_time`, `last_login_ip`, `last_login_region`, `last_login_at`, `register_ip`, `register_device`, `register_channel`, `real_auth_status`, `created_at`, `updated_at`, `deleted_at`) VALUES (934035802554576898, 'testuser', '', '13800138002', 'test@example.com', '$2y$12$TMbO5wisUaMV8n977794heEtiFCzwc7zpt1s.k27hsUbBf9HHQ.iG', 'salt', 1, '', NULL, '', '', NULL, '', '', 'web', 1, '2026-07-23 02:21:47', '2026-07-26 09:57:57', NULL);
COMMIT;

-- ----------------------------
-- Table structure for wf_flow_apply
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_apply`;
CREATE TABLE `wf_flow_apply` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '申请单ID',
  `apply_no` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '审批单号',
  `flow_type_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '审批类型ID',
  `flow_def_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '流程模板ID',
  `title` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '单据标题',
  `apply_user_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '发起人UID',
  `dept_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '发起人部门ID',
  `form_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '表单提交内容JSON',
  `current_node_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '当前待审批节点ID',
  `current_approve_uid` bigint unsigned NOT NULL DEFAULT '0' COMMENT '当前待处理审批人',
  `apply_status` tinyint NOT NULL DEFAULT '0' COMMENT '单据总状态',
  `remark` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '发起人备注',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_apply_no` (`apply_no`) USING BTREE,
  KEY `idx_apply_user_id` (`apply_user_id`) USING BTREE,
  KEY `idx_flow_def_id` (`flow_def_id`) USING BTREE,
  KEY `idx_apply_status` (`apply_status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733862003212322 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of wf_flow_apply
-- ----------------------------
BEGIN;
INSERT INTO `wf_flow_apply` (`id`, `apply_no`, `flow_type_id`, `flow_def_id`, `title`, `apply_user_id`, `dept_id`, `form_data`, `current_node_id`, `current_approve_uid`, `apply_status`, `remark`, `created_at`, `updated_at`) VALUES (920733862003212321, 'WF20260726001', 920733862004256487, 920733862004256572, '27日请假一天', 934035802554576897, 0, '{\"days\":\"1\",\"times\":\"2026-07-27\",\"reasons\":\"\\u51fa\\u53bb\\u73a9\",\"persons\":\"admin\"}', 0, 0, 0, '2026年07月26日10:46:38', '2026-07-26 02:47:00', '2026-07-26 02:47:00');
COMMIT;

-- ----------------------------
-- Table structure for wf_flow_approve_record
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_approve_record`;
CREATE TABLE `wf_flow_approve_record` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `apply_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '关联申请单ID',
  `node_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '流程节点ID',
  `approve_user_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '操作审批人UID',
  `action_type` tinyint NOT NULL DEFAULT '0' COMMENT '操作类型',
  `target_user_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '转审/加签目标人ID',
  `approve_opinion` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '审批意见',
  `attach_files` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '附件地址JSON数组',
  `operate_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_apply_id` (`apply_id`) USING BTREE,
  KEY `idx_approve_user_id` (`approve_user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733862003213621 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of wf_flow_approve_record
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wf_flow_cc_user
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_cc_user`;
CREATE TABLE `wf_flow_cc_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `apply_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '申请单ID',
  `cc_uid` bigint unsigned NOT NULL DEFAULT '0' COMMENT '被抄送用户ID',
  `is_read` tinyint NOT NULL DEFAULT '0' COMMENT '0未读 1已读',
  `read_time` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_apply_cc_uid` (`apply_id`,`cc_uid`) USING BTREE,
  KEY `idx_cc_uid` (`cc_uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733862003203210 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of wf_flow_cc_user
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wf_flow_definition
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_definition`;
CREATE TABLE `wf_flow_definition` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '流程定义ID',
  `flow_type_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '关联流程类型ID',
  `flow_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '流程名称',
  `version` int NOT NULL DEFAULT '1' COMMENT '版本号',
  `remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注说明',
  `apply_scope` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '可发起人员范围JSON',
  `is_publish` tinyint NOT NULL DEFAULT '0' COMMENT '是否发布 0草稿 1已发布',
  `created_by` bigint unsigned NOT NULL DEFAULT '0' COMMENT '创建人用户ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_flow_type_id` (`flow_type_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733862004256573 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of wf_flow_definition
-- ----------------------------
BEGIN;
INSERT INTO `wf_flow_definition` (`id`, `flow_type_id`, `flow_name`, `version`, `remark`, `apply_scope`, `is_publish`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862004256572, 920733862004256487, '日常请假审批流程V1', 1, '第一版', '[]', 1, 934035802554576897, '2026-07-26 02:45:35', '2026-07-26 02:45:45', NULL);
COMMIT;

-- ----------------------------
-- Table structure for wf_flow_form
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_form`;
CREATE TABLE `wf_flow_form` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `flow_def_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '绑定流程定义ID',
  `field_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字段中文名称',
  `field_key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字段英文标识',
  `field_type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '组件类型',
  `field_options` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '选项JSON',
  `is_required` tinyint NOT NULL DEFAULT '1' COMMENT '是否必填',
  `sort` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_flow_def_id` (`flow_def_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733862003211472 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of wf_flow_form
-- ----------------------------
BEGIN;
INSERT INTO `wf_flow_form` (`id`, `flow_def_id`, `field_name`, `field_key`, `field_type`, `field_options`, `is_required`, `sort`, `created_at`) VALUES (920733862003211468, 920733862004256572, '请假天数', 'days', 'input', '[]', 1, 0, '2026-07-26 02:46:18');
INSERT INTO `wf_flow_form` (`id`, `flow_def_id`, `field_name`, `field_key`, `field_type`, `field_options`, `is_required`, `sort`, `created_at`) VALUES (920733862003211469, 920733862004256572, '请假时间', 'times', 'date', '[]', 1, 1, '2026-07-26 02:46:18');
INSERT INTO `wf_flow_form` (`id`, `flow_def_id`, `field_name`, `field_key`, `field_type`, `field_options`, `is_required`, `sort`, `created_at`) VALUES (920733862003211470, 920733862004256572, '请假事由', 'reasons', 'textarea', '[]', 1, 2, '2026-07-26 02:46:18');
INSERT INTO `wf_flow_form` (`id`, `flow_def_id`, `field_name`, `field_key`, `field_type`, `field_options`, `is_required`, `sort`, `created_at`) VALUES (920733862003211471, 920733862004256572, '请假人', 'persons', 'input', '[]', 1, 3, '2026-07-26 02:46:18');
COMMIT;

-- ----------------------------
-- Table structure for wf_flow_node
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_node`;
CREATE TABLE `wf_flow_node` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '节点ID',
  `flow_def_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '关联流程定义ID',
  `node_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '节点名称',
  `node_sort` int NOT NULL DEFAULT '1' COMMENT '节点执行顺序',
  `approve_type` tinyint NOT NULL DEFAULT '2' COMMENT '审批人员类型',
  `approve_target` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '审批目标值JSON',
  `node_mode` tinyint NOT NULL DEFAULT '1' COMMENT '节点审批模式',
  `can_reject` tinyint NOT NULL DEFAULT '1' COMMENT '是否可驳回',
  `can_add_sign` tinyint NOT NULL DEFAULT '1' COMMENT '是否允许加签',
  `can_transfer` tinyint NOT NULL DEFAULT '1' COMMENT '是否允许转审',
  `back_node_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '驳回回退节点ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_flow_def_id` (`flow_def_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733862004251217 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of wf_flow_node
-- ----------------------------
BEGIN;
INSERT INTO `wf_flow_node` (`id`, `flow_def_id`, `node_name`, `node_sort`, `approve_type`, `approve_target`, `node_mode`, `can_reject`, `can_add_sign`, `can_transfer`, `back_node_id`, `created_at`) VALUES (920733862004251216, 920733862004256572, '领导', 1, 1, '[]', 1, 1, 1, 1, 0, '2026-07-26 02:46:18');
COMMIT;

-- ----------------------------
-- Table structure for wf_flow_node_condition
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_node_condition`;
CREATE TABLE `wf_flow_node_condition` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `flow_def_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '所属流程ID',
  `pre_node_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '上一个节点ID',
  `condition_field` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '条件字段',
  `condition_operator` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '运算符',
  `condition_value` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '阈值数值',
  `jump_node_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '满足条件跳转节点ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_flow_def_id` (`flow_def_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733862004251209 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of wf_flow_node_condition
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wf_flow_type
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_type`;
CREATE TABLE `wf_flow_type` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `type_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '流程类型名称',
  `type_code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '唯一编码',
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '前端图标',
  `sort` int NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '状态 0禁用 1启用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_type_code` (`type_code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=920733862004256492 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of wf_flow_type
-- ----------------------------
BEGIN;
INSERT INTO `wf_flow_type` (`id`, `type_name`, `type_code`, `icon`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862004256487, '请假审批', 'leave', 'Calendar', 100, 1, '2026-07-25 11:18:13', '2026-07-25 11:18:13', NULL);
INSERT INTO `wf_flow_type` (`id`, `type_name`, `type_code`, `icon`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862004256488, '费用报销', 'reimburse', 'Wallet', 90, 1, '2026-07-25 11:18:13', '2026-07-25 11:18:13', NULL);
INSERT INTO `wf_flow_type` (`id`, `type_name`, `type_code`, `icon`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862004256489, '采购申请', 'purchase', 'ShoppingCart', 80, 1, '2026-07-25 11:18:13', '2026-07-25 11:18:13', NULL);
INSERT INTO `wf_flow_type` (`id`, `type_name`, `type_code`, `icon`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862004256490, '商品上架审批', 'product_online', 'Goods', 70, 1, '2026-07-25 11:18:13', '2026-07-25 11:18:13', NULL);
INSERT INTO `wf_flow_type` (`id`, `type_name`, `type_code`, `icon`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (920733862004256491, '客户入驻审批', 'customer_audit', 'UserFilled', 60, 1, '2026-07-25 11:18:13', '2026-07-25 11:18:13', NULL);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
