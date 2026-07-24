/*
 Navicat Premium Dump SQL

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 80046 (8.0.46)
 Source Host           : localhost:3306
 Source Schema         : sunny_cloud

 Target Server Type    : MySQL
 Target Server Version : 80046 (8.0.46)
 File Encoding         : 65001

 Date: 24/07/2026 20:04:33
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for auth_menus
-- ----------------------------
DROP TABLE IF EXISTS `auth_menus`;
CREATE TABLE `auth_menus`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `parent_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级菜单ID，0表示顶级菜单',
  `menu_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单名称，如：用户管理',
  `menu_icon` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单图标，如：el-icon-user',
  `menu_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '前端路由路径，如：/user/list',
  `component` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '前端组件路径，如：user/Index',
  `permission_code` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '关联的权限标识，用于按钮级控制',
  `menu_sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序权重，值越大越靠前',
  `menu_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0=禁用，1=启用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime NULL DEFAULT NULL COMMENT '删除时间（软删除）',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_parent_id`(`parent_id` ASC) USING BTREE,
  INDEX `idx_permission_code`(`permission_code` ASC) USING BTREE,
  INDEX `idx_status`(`menu_status` ASC) USING BTREE,
  INDEX `idx_deleted_at`(`deleted_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733863034403266 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '菜单/功能表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of auth_menus
-- ----------------------------
INSERT INTO `auth_menus` VALUES (920733860755423248, 0, '首页', 'el-icon-s-home', '/backend/dashboard', '/backend/dashboard', 'dashboardview', 10, 1, '2026-07-23 09:58:06', '2026-07-24 11:49:41', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423249, 0, '内容管理', 'el-icon-document', '/backend/content', 'Layout', 'contentview', 2, 1, '2026-07-23 09:58:06', '2026-07-23 10:04:22', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423250, 0, '文章管理', 'el-icon-edit-outlined', '/backend/article', 'content/article/index', 'articleview', 1, 1, '2026-07-23 09:58:06', '2026-07-23 10:49:18', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423251, 920733860755423250, '文章列表', 'el-icon-document', '/backend/article/index', 'content/article/List', 'articlelist', 1, 1, '2026-07-23 09:58:06', '2026-07-23 10:49:12', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423252, 920733860755423250, '添加文章', 'el-icon-plus', '/backend/article/add', 'content/article/Add', 'articleadd', 2, 0, '2026-07-23 09:58:06', '2026-07-23 10:51:27', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423253, 920733860755423249, '文章分类', 'el-icon-folder-opened', '/backend/categories', '/backend/categories', 'articlecategoryview', 2, 1, '2026-07-23 09:58:06', '2026-07-24 11:50:45', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423254, 920733860755423249, '分类管理', 'el-icon-folder-opened', '/backend/category', 'content/category/index', 'categoryview', 3, 1, '2026-07-23 09:58:06', '2026-07-23 10:07:25', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423255, 0, '运营管理', 'el-icon-present', '/backend/operation', 'Layout', 'operationview', 3, 1, '2026-07-23 09:58:06', '2026-07-23 10:04:03', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423256, 920733860755423255, '广告位管理', 'el-icon-picture', '/backend/adslots', 'operation/adslots/index', 'adslotsview', 1, 1, '2026-07-23 09:58:06', '2026-07-23 10:07:53', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423257, 920733860755423255, '广告管理', 'el-icon-office-building', '/backend/adpositions', 'operation/adpositions/index', 'adpositionsview', 2, 1, '2026-07-23 09:58:06', '2026-07-23 10:07:57', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423258, 920733860755423255, '友情链接', 'el-icon-link', '/backend/friendlinks', 'operation/friendlinks/index', 'friendlinksview', 3, 1, '2026-07-23 09:58:06', '2026-07-23 10:08:01', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423259, 920733860755423255, '用户留言', 'el-icon-chat-line', '/backend/feedbacks', 'operation/feedbacks/index', 'feedbacksview', 4, 1, '2026-07-23 09:58:06', '2026-07-23 10:08:04', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423260, 920733860755423255, '招聘职位', 'el-icon-user', '/backend/bossjob', 'operation/bossjob/index', 'bossjobview', 5, 1, '2026-07-23 09:58:06', '2026-07-23 10:08:08', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423261, 0, '系统管理', 'el-icon-setting', '/backend/system', 'Layout', 'systemview', 4, 1, '2026-07-23 09:58:06', '2026-07-23 10:03:45', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423262, 920733860755423261, '系统设置', 'el-icon-tools', '/backend/siteconfigs', 'system/config/index', 'configview', 1, 1, '2026-07-23 09:58:06', '2026-07-23 12:31:42', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423263, 920733860755423261, '菜单管理', 'el-icon-menu', '/backend/menu', 'system/menu/index', 'menumanage', 2, 1, '2026-07-23 09:58:06', '2026-07-23 10:08:18', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423264, 0, '权限管理', 'el-icon-lock', '/backend/permission', 'system/permission/index', 'permissionmanage', 3, 1, '2026-07-23 09:58:06', '2026-07-23 10:08:43', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423265, 920733860755423264, '权限规则', 'el-icon-key', '/backend/permission/rules', 'system/permission/Rules', 'permissionrules', 1, 1, '2026-07-23 09:58:06', '2026-07-23 10:08:47', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423266, 0, '角色管理', 'el-icon-user', '/backend/permission/role', 'system/permission/Role', 'rolemanage', 2, 1, '2026-07-23 09:58:06', '2026-07-23 10:08:50', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423267, 920733860755423266, '角色列表', 'el-icon-user', '/backend/permission/role/index', 'system/permission/RoleList', 'rolelist', 1, 1, '2026-07-23 09:58:06', '2026-07-23 10:49:06', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423268, 920733860755423266, '角色权限', 'el-icon-key', '/backend/permission/role/perm', 'system/permission/RolePerm', 'roleperm', 2, 1, '2026-07-23 09:58:06', '2026-07-23 10:08:55', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423269, 920733860755423266, '角色菜单', 'el-icon-menu', '/backend/permission/role/menu', 'system/permission/RoleMenu', 'rolemenu', 3, 1, '2026-07-23 09:58:06', '2026-07-23 10:08:58', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423270, 920733860755423261, '用户管理', 'el-icon-user', '/backend/user', 'system/user/index', 'usermanage', 4, 1, '2026-07-23 09:58:06', '2026-07-23 10:09:01', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423271, 920733860755423261, '操作日志', 'el-icon-document', '/backend/operationlog/index', 'system/log/index', 'logview', 5, 1, '2026-07-23 09:58:06', '2026-07-23 10:49:00', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423272, 0, '书签管理', 'el-icon-star-off', '/backend/bookmark', 'bookmark/index', 'bookmarkview', 5, 1, '2026-07-23 09:58:06', '2026-07-23 10:03:15', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423273, 920733860755423272, '书签列表', 'el-icon-star-on', '/backend/bookmark/index', 'bookmark/List', 'bookmarklist', 1, 1, '2026-07-23 09:58:06', '2026-07-23 10:45:39', NULL);
INSERT INTO `auth_menus` VALUES (920733860755423274, 920733860755423272, '我的书签', 'el-icon-collection', '/backend/bookmark/my', 'bookmark/My', 'bookmarkmy', 2, 0, '2026-07-23 09:58:06', '2026-07-23 10:46:27', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403263, 0, '权限菜单分类管理', 'Lock', '', '', 'permission', 90, 1, '2026-07-24 11:54:43', '2026-07-24 11:54:43', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403264, 920733863034403263, '角色管理', '', '/backend/roles', 'roles/Index', 'role.view', 15, 1, '2026-07-24 11:54:43', '2026-07-24 11:54:43', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403265, 920733863034403263, '权限管理', '', '/backend/permissions', 'permissions/Index', 'permission.view', 25, 1, '2026-07-24 12:00:54', '2026-07-24 12:00:54', NULL);

-- ----------------------------
-- Table structure for auth_permissions
-- ----------------------------
DROP TABLE IF EXISTS `auth_permissions`;
CREATE TABLE `auth_permissions`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `parent_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级权限ID，用于树形结构',
  `per_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '权限名称，如：用户删除',
  `per_code` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '权限唯一标识，如：user:delete',
  `per_type` enum('menu','button','api') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'api' COMMENT '权限类型：menu=菜单，button=按钮，api=接口',
  `per_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '' COMMENT '前端路由路径或API路径，如：/user/delete',
  `per_method` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '' COMMENT 'HTTP方法，GET/POST/PUT/DELETE，仅 type=api 时有效',
  `per_icon` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '' COMMENT '菜单图标，仅 type=menu 时有效',
  `per_sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序权重，值越大越靠前',
  `per_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0=禁用，1=启用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime NULL DEFAULT NULL COMMENT '删除时间（软删除）',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_code`(`per_code` ASC) USING BTREE,
  INDEX `idx_parent_id`(`parent_id` ASC) USING BTREE,
  INDEX `idx_type`(`per_type` ASC) USING BTREE,
  INDEX `idx_status`(`per_status` ASC) USING BTREE,
  INDEX `idx_deleted_at`(`deleted_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733862755423293 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '权限规则表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of auth_permissions
-- ----------------------------
INSERT INTO `auth_permissions` VALUES (920733862755423246, 0, '首页仪表盘', 'dashboard:view', 'menu', '/dashboard', '', 'el-icon-s-home', 100, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423247, 0, '内容管理', 'content:view', 'menu', '/content', '', 'el-icon-document-copy', 90, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423248, 0, '运营管理', 'operation:view', 'menu', '/operation', '', 'el-icon-present', 80, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423249, 0, '系统管理', 'system:view', 'menu', '/system', '', 'el-icon-setting', 70, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423250, 0, '书签管理', 'bookmark:view', 'menu', '/bookmark', '', 'el-icon-star-off', 60, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423251, 920733862755423247, '文章管理', 'article:view', 'menu', '/content/article', '', 'el-icon-edit-outline', 10, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423252, 920733862755423247, '分类管理', 'category:view', 'menu', '/content/category', '', 'el-icon-folder-opened', 9, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423253, 920733862755423247, '友情链接', 'friendlink:view', 'menu', '/content/friendlink', '', 'el-icon-link', 8, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423254, 920733862755423248, '横幅管理', 'banner:view', 'menu', '/operation/banner', '', 'el-icon-picture', 20, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423255, 920733862755423248, '广告位管理', 'ad:view', 'menu', '/operation/ad', '', 'el-icon-office-building', 19, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423256, 920733862755423248, '留言管理', 'feedback:view', 'menu', '/operation/feedback', '', 'el-icon-chat-line-round', 18, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423257, 920733862755423249, '系统设置', 'config:view', 'menu', '/system/config', '', 'el-icon-tools', 30, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423258, 920733862755423249, '菜单管理', 'menu:view', 'menu', '/system/menu', '', 'el-icon-menu', 29, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423259, 920733862755423249, '权限管理', 'permission:view', 'menu', '/system/permission', '', 'el-icon-lock', 28, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423260, 920733862755423249, '用户管理', 'user:view', 'menu', '/system/user', '', 'el-icon-user', 27, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423261, 920733862755423249, '操作日志', 'log:view', 'menu', '/system/log', '', 'el-icon-document', 26, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423262, 920733862755423250, '书签列表', 'bookmark:list', 'menu', '/bookmark/list', '', 'el-icon-star-on', 10, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423263, 920733862755423250, '我的书签', 'bookmark:my', 'menu', '/bookmark/my', '', 'el-icon-collection', 9, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423264, 920733862755423251, '文章列表', 'article:list', 'button', '/content/article/list', '', '', 10, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423265, 920733862755423251, '添加文章', 'article:add', 'button', '/content/article/add', '', '', 9, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423266, 920733862755423251, '编辑文章', 'article:edit', 'button', '/content/article/edit', '', '', 8, 1, '2026-07-23 08:00:35', '2026-07-23 08:00:35', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423267, 920733862755423251, '删除文章', 'article:delete', 'button', '/content/article/delete', '', '', 7, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423268, 920733862755423252, '添加分类', 'category:add', 'button', '/content/category/add', '', '', 5, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423269, 920733862755423252, '编辑分类', 'category:edit', 'button', '/content/category/edit', '', '', 4, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423270, 920733862755423252, '删除分类', 'category:delete', 'button', '/content/category/delete', '', '', 3, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423271, 920733862755423253, '添加友情链接', 'friendlink:add', 'button', '/content/friendlink/add', '', '', 5, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423272, 920733862755423253, '编辑友情链接', 'friendlink:edit', 'button', '/content/friendlink/edit', '', '', 4, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423273, 920733862755423253, '删除友情链接', 'friendlink:delete', 'button', '/content/friendlink/delete', '', '', 3, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423274, 920733862755423254, '添加横幅', 'banner:add', 'button', '/operation/banner/add', '', '', 5, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423275, 920733862755423254, '编辑横幅', 'banner:edit', 'button', '/operation/banner/edit', '', '', 4, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423276, 920733862755423254, '删除横幅', 'banner:delete', 'button', '/operation/banner/delete', '', '', 3, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423277, 920733862755423255, '添加广告位', 'ad:add', 'button', '/operation/ad/add', '', '', 5, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423278, 920733862755423255, '编辑广告位', 'ad:edit', 'button', '/operation/ad/edit', '', '', 4, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423279, 920733862755423255, '删除广告位', 'ad:delete', 'button', '/operation/ad/delete', '', '', 3, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423280, 920733862755423256, '处理留言', 'feedback:handle', 'button', '/operation/feedback/handle', '', '', 5, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423281, 920733862755423256, '删除留言', 'feedback:delete', 'button', '/operation/feedback/delete', '', '', 3, 1, '2026-07-23 08:00:36', '2026-07-23 08:00:36', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423282, 0, '角色管理', 'role:view', 'menu', '/system/permission/role', '', '', 10, 1, '2026-07-23 08:04:21', '2026-07-23 08:04:21', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423283, 920733862755423260, '添加用户', 'user:add', 'button', '/system/user/add', '', '', 10, 1, '2026-07-23 08:06:48', '2026-07-23 08:06:48', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423284, 920733862755423283, '添加用户API', 'api:user:add', 'api', '/api/user/add', 'POST', '', 5, 1, '2026-07-23 08:06:48', '2026-07-23 08:06:48', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423285, 920733862755423260, '编辑用户', 'user:edit', 'button', '/system/user/edit', '', '', 9, 1, '2026-07-23 08:06:48', '2026-07-23 08:06:48', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423286, 920733862755423285, '编辑用户API', 'api:user:edit', 'api', '/api/user/edit', 'PUT', '', 4, 1, '2026-07-23 08:06:48', '2026-07-23 08:06:48', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423287, 920733862755423260, '删除用户', 'user:delete', 'button', '/system/user/delete', '', '', 8, 1, '2026-07-23 08:06:48', '2026-07-23 08:06:48', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423288, 920733862755423287, '删除用户API', 'api:user:delete', 'api', '/api/user/delete', 'DELETE', '', 3, 1, '2026-07-23 08:06:48', '2026-07-23 08:06:48', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423289, 920733862755423264, '文章列表API', 'api:article:list', 'api', '/api/article/list', 'GET', '', 10, 1, '2026-07-23 08:07:06', '2026-07-23 08:07:06', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423290, 920733862755423265, '添加文章API', 'api:article:add', 'api', '/api/article/add', 'POST', '', 9, 1, '2026-07-23 08:07:06', '2026-07-23 08:07:06', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423291, 920733862755423266, '编辑文章API', 'api:article:edit', 'api', '/api/article/edit', 'PUT', '', 8, 1, '2026-07-23 08:07:06', '2026-07-23 08:07:06', NULL);
INSERT INTO `auth_permissions` VALUES (920733862755423292, 920733862755423267, '删除文章API', 'api:article:delete', 'api', '/api/article/delete', 'DELETE', '', 7, 1, '2026-07-23 08:07:06', '2026-07-23 08:07:06', NULL);

-- ----------------------------
-- Table structure for auth_role
-- ----------------------------
DROP TABLE IF EXISTS `auth_role`;
CREATE TABLE `auth_role`  (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `role_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '角色名称 如 超级管理员',
  `role_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '角色唯一标识（代码鉴权使用，如 finance_admin）',
  `role_type` tinyint(1) NOT NULL DEFAULT 2 COMMENT '角色类型: 1=系统内置 2=用户自定义',
  `role_sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序号',
  `data_scope` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '数据权限范围 1全部数据 2本部门及下级 3本部门 4仅本人数据 5自定义指定部门',
  `scope_departments` json NULL COMMENT '指定部门IDs，JSON格式',
  `role_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '0禁用 1启用',
  `role_remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '角色备注',
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_role_code`(`role_code` ASC, `deleted_at` ASC) USING BTREE,
  INDEX `idx_status`(`role_status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733860755423258 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '角色信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of auth_role
-- ----------------------------
INSERT INTO `auth_role` VALUES (920733860755423247, '超级管理员', 'super_admin', 1, 100, 1, NULL, 1, '系统内置超级管理员，拥有全部数据权限', '2026-07-23 07:58:27', '2026-07-24 11:54:43', NULL);
INSERT INTO `auth_role` VALUES (920733860755423248, '系统管理员', 'system_admin', 1, 90, 1, NULL, 1, '负责系统运维、基础配置、用户管理、菜单权限分配', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` VALUES (920733860755423249, '内容管理员', 'content_admin', 1, 80, 2, NULL, 1, '负责内容管理（文章、分类、友情链接等），可管理本部门及下级内容', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` VALUES (920733860755423250, '运营管理员', 'operation_admin', 1, 70, 2, NULL, 1, '负责运营管理（横幅、广告位、留言等），可管理本部门及下级数据', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` VALUES (920733860755423251, '内容编辑', 'content_editor', 2, 60, 3, NULL, 1, '负责文章内容的编辑、发布、修改，仅可操作本部门数据', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` VALUES (920733860755423252, '运营专员', 'operation_specialist', 2, 50, 3, NULL, 1, '负责横幅、广告位、留言等日常运营操作，仅可操作本部门数据', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` VALUES (920733860755423253, '访客用户', 'guest_user', 2, 10, 4, NULL, 1, '仅可查看公开内容，无编辑权限，数据仅限本人相关', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` VALUES (920733860755423254, '部门经理', 'dept_manager', 2, 40, 2, NULL, 1, '可管理本部门及下级部门的所有数据，包括内容审核、运营审批等', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` VALUES (920733860755423255, '人事管理员', 'hr_admin', 2, 30, 2, NULL, 1, '负责招聘管理、用户管理、组织架构等，可管理本部门及下级数据', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` VALUES (920733860755423256, '财务管理员', 'finance_admin', 2, 20, 1, NULL, 1, '负责财务相关数据查看与管理，拥有全部财务数据权限', '2026-07-23 07:58:27', '2026-07-23 07:58:27', NULL);
INSERT INTO `auth_role` VALUES (920733860755423257, '管理员', 'admin', 1, 90, 1, NULL, 1, '系统内置管理员', '2026-07-24 11:54:43', '2026-07-24 11:54:43', NULL);

-- ----------------------------
-- Table structure for auth_role_menus
-- ----------------------------
DROP TABLE IF EXISTS `auth_role_menus`;
CREATE TABLE `auth_role_menus`  (
  `role_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID（关联 auth_roles.id）',
  `menu_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '菜单ID（关联 auth_menus.id）',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`role_id`, `menu_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '角色-菜单关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of auth_role_menus
-- ----------------------------
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423001, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423002, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423003, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423004, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423005, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423006, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423007, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423008, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423009, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423010, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423011, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423012, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423013, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423014, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423015, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423016, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423017, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423018, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423019, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423020, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423021, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423022, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423023, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423024, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423025, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423026, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423027, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423028, '2026-07-23 08:25:35');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423029, '2026-07-23 08:25:35');

-- ----------------------------
-- Table structure for auth_role_permissions
-- ----------------------------
DROP TABLE IF EXISTS `auth_role_permissions`;
CREATE TABLE `auth_role_permissions`  (
  `role_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID（关联 auth_roles.id）',
  `permission_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '权限ID（关联 auth_permissions.id）',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`role_id`, `permission_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '角色-权限关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of auth_role_permissions
-- ----------------------------
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423246, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423247, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423248, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423249, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423250, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423251, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423252, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423253, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423254, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423255, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423256, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423257, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423258, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423259, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423260, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423261, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423262, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423263, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423264, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423265, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423266, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423267, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423268, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423269, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423270, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423271, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423272, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423273, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423274, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423275, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423276, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423277, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423278, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423279, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423280, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423281, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423282, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423283, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423284, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423285, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423286, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423287, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423288, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423289, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423290, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423291, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423292, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423248, 920733862755423246, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423248, 920733862755423249, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423248, 920733862755423257, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423248, 920733862755423258, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423248, 920733862755423259, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423248, 920733862755423260, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423248, 920733862755423261, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423248, 920733862755423282, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423248, 920733862755423283, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423248, 920733862755423284, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423248, 920733862755423285, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423248, 920733862755423286, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423248, 920733862755423287, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423248, 920733862755423288, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423247, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423251, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423252, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423253, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423264, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423265, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423266, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423267, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423268, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423269, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423270, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423271, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423272, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423273, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423289, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423290, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423291, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423249, 920733862755423292, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423250, 920733862755423248, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423250, 920733862755423254, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423250, 920733862755423255, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423250, 920733862755423256, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423250, 920733862755423274, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423250, 920733862755423275, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423250, 920733862755423276, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423250, 920733862755423277, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423250, 920733862755423278, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423250, 920733862755423279, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423250, 920733862755423280, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423250, 920733862755423281, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423251, 920733862755423251, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423251, 920733862755423264, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423251, 920733862755423265, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423251, 920733862755423266, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423251, 920733862755423267, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423252, 920733862755423254, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423252, 920733862755423255, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423252, 920733862755423256, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423252, 920733862755423274, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423252, 920733862755423275, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423252, 920733862755423277, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423252, 920733862755423278, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423252, 920733862755423280, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423253, 920733862755423246, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423253, 920733862755423264, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423247, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423248, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423251, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423252, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423253, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423254, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423255, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423256, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423264, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423265, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423266, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423267, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423268, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423269, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423270, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423271, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423272, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423273, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423274, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423275, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423276, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423277, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423278, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423279, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423280, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423254, 920733862755423281, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423255, 920733862755423260, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423255, 920733862755423283, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423255, 920733862755423285, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423255, 920733862755423287, '2026-07-23 09:49:06');
INSERT INTO `auth_role_permissions` VALUES (920733860755423256, 920733862755423246, '2026-07-23 09:49:06');

-- ----------------------------
-- Table structure for auth_user_role
-- ----------------------------
DROP TABLE IF EXISTS `auth_user_role`;
CREATE TABLE `auth_user_role`  (
  `user_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `role_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`user_id`, `role_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733861755423246 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '用户-角色关联' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of auth_user_role
-- ----------------------------
INSERT INTO `auth_user_role` VALUES (920733860755423002, 920733860755423247, '2026-07-23 09:50:36');
INSERT INTO `auth_user_role` VALUES (920733860755423003, 920733860755423251, '2026-07-23 09:50:36');
INSERT INTO `auth_user_role` VALUES (920733860755423004, 920733860755423252, '2026-07-23 09:50:36');
INSERT INTO `auth_user_role` VALUES (920733860755423005, 920733860755423254, '2026-07-23 09:50:36');
INSERT INTO `auth_user_role` VALUES (920733860755423006, 920733860755423253, '2026-07-23 09:50:36');
INSERT INTO `auth_user_role` VALUES (920733860755423007, 920733860755423256, '2026-07-23 09:50:36');
INSERT INTO `auth_user_role` VALUES (920733860755423008, 920733860755423253, '2026-07-23 09:50:36');
INSERT INTO `auth_user_role` VALUES (934035802554576897, 920733860755423247, '2026-07-23 05:32:44');
INSERT INTO `auth_user_role` VALUES (934035802554576897, 920733860755423248, '2026-07-23 09:50:36');

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  INDEX `cache_expiration_index`(`expiration` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache
-- ----------------------------

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  INDEX `cache_locks_expiration_index`(`expiration` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache_locks
-- ----------------------------

-- ----------------------------
-- Table structure for category
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `category_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `parent_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级分类ID 0是一级分类',
  `show_type` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '可见性类型 0=全部可见 1=指定客户可见 2=指定客户不可见',
  `cat_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 0=隐藏 1=显示',
  `level` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '级别 1一级 2二级 3三级',
  `sort_order` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `description` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类描述/SEO说明',
  `cat_remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `created_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '创建人',
  `updated_at` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '更新人',
  `deleted_at` datetime NULL DEFAULT NULL COMMENT '删除时间',
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '删除人',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `category_parent_id_index`(`parent_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 935126090643669000 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '分类表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of category
-- ----------------------------
INSERT INTO `category` VALUES (935126090643668993, '视频', 0, 0, 1, 1, 0, '视频网站', '视频网站', '2026-07-22 10:26:47', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `category` VALUES (935126090643668994, '技术栈', 0, 0, 1, 1, 0, '程序开发技术栈', '技术栈', '2026-07-22 10:27:44', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `category` VALUES (935126090643668995, '电商', 0, 0, 1, 1, 0, '淘宝京东拼多多', '电商网站', '2026-07-22 10:28:46', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `category` VALUES (935126090643668996, '公司', 0, 0, 1, 1, 0, '自定义公司本地', '公司', '2026-07-22 10:29:40', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `category` VALUES (935126090643668997, '工具', 0, 0, 1, 1, 0, '工具合集', '工具', '2026-07-22 10:30:33', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `category` VALUES (935126090643668998, 'AI', 0, 0, 1, 1, 0, 'AI工具', 'AI', '2026-07-22 10:31:04', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `category` VALUES (935126090643668999, '搜索', 0, 0, 1, 1, 0, '各类搜索网盘', '网盘搜索', '2026-07-22 10:31:46', NULL, NULL, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `cancelled_at` int NULL DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of job_batches
-- ----------------------------

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED NULL DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index`(`queue` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jobs
-- ----------------------------

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` VALUES (4, '2026_07_24_000001_create_category_table', 2);
INSERT INTO `migrations` VALUES (5, '2026_07_24_000002_create_user_account_table', 999);
INSERT INTO `migrations` VALUES (6, '2026_07_24_000003_create_auth_menu_table', 999);
INSERT INTO `migrations` VALUES (7, '2026_07_24_000004_create_auth_role_table', 999);
INSERT INTO `migrations` VALUES (8, '2026_07_24_000005_create_auth_permission_table', 999);

-- ----------------------------
-- Table structure for operation_log
-- ----------------------------
DROP TABLE IF EXISTS `operation_log`;
CREATE TABLE `operation_log`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `operator_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作人ID',
  `operator_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '操作人名称',
  `biz_type` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '业务模块类型 product/category/customer',
  `activity_type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '活动类型如product_created',
  `action` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '操作类型 (INSERT/UPDATE/DELETE/LOGIN)',
  `biz_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '目标实体ID',
  `biz_label` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '高亮展示文本',
  `old_value` json NULL COMMENT '修改前的数据快照 (JSON格式)',
  `new_value` json NULL COMMENT '修改后的数据快照 (JSON格式)',
  `operator_status` tinyint NOT NULL DEFAULT 1 COMMENT '操作状态 (0:失败, 1:成功)',
  `error_msg` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '错误信息 (失败时记录)',
  `client_ip` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '客户端IP',
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户浏览器/设备信息',
  `request_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '触发日志的API URL',
  `method_fun` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '触发日志的方法名',
  `created_at` datetime(6) NULL DEFAULT NULL COMMENT '发生时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `merchant_activity_log_operator_id_index`(`operator_id` ASC) USING BTREE,
  INDEX `merchant_activity_log_biz_index`(`biz_type` ASC, `biz_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 935126090643669063 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '用户操作动态表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of operation_log
-- ----------------------------

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sessions_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `sessions_last_activity_index`(`last_activity` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sessions
-- ----------------------------
INSERT INTO `sessions` VALUES ('5QquVawJ2QQKHWKQmqa4S1C034GXGQroe0SYn8gu', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; zh-CN) WindowsPowerShell/5.1.19041.6328', 'eyJfdG9rZW4iOiJ2bzBueGVzaGJKcFpwWUptZnI1WllETWhmT1hqUkRud3J1MFhzSDRzIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9iYWNrZW5kXC9sb2dpbiIsInJvdXRlIjoiYmFja2VuZC5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX2JhY2tlbmRfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6OTM0MDM1ODAyNTU0NTc2ODk3fQ==', 1784893577);
INSERT INTO `sessions` VALUES ('AMnDi6Qv6unEvJnL1wIlcDs2uin4EBNIoUoXznyj', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; zh-CN) WindowsPowerShell/5.1.19041.6328', 'eyJfdG9rZW4iOiJUTjczRk9ucW9GU3JuakIxZ09STXo2WjRGV0N0MFg4S2dRU3FudnlSIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9iYWNrZW5kXC9sb2dpbiIsInJvdXRlIjoiYmFja2VuZC5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX2JhY2tlbmRfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6OTM0MDM1ODAyNTU0NTc2ODk3fQ==', 1784892159);
INSERT INTO `sessions` VALUES ('GsdBZXQO6s20XFQz0ThBmWLLzomqPxDjstjzpxmA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; zh-CN) WindowsPowerShell/5.1.19041.6328', 'eyJfdG9rZW4iOiIzRldNeDdCNjZPMUxjWHRXOUU1WUJqM3JGWU1KYU40VmVBQm9STFRxIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9iYWNrZW5kXC9uZXdzXC9jYXRlZ29yaWVzIiwicm91dGUiOiJiYWNrZW5kLmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1784891380);
INSERT INTO `sessions` VALUES ('hj58Tka1bxgHMNIHzhKr9usMLCO2C1TCLmOJtiHJ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJiRkhNZVZEQlVKQ3VmU1QyZWRGV3ZiMXpKY1dtVnE2b25aRExBV1RsIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9iYWNrZW5kXC9uZXdzXC9jYXRlZ29yaWVzIiwicm91dGUiOiJiYWNrZW5kLmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1784891423);
INSERT INTO `sessions` VALUES ('HjMOEP7mlrDA73qPnQVV5GZZn24NJhqRi6SZUnaN', NULL, '127.0.0.1', 'Symfony', 'eyJfdG9rZW4iOiI1Rlp5WTFxTmlXQmlKaXdvSXJINDZqcEpGcVp3dWcyMUFwMkt2TWNmIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1784892226);
INSERT INTO `sessions` VALUES ('jBHa4xmcEB6XNMfOirS4jbqVCVj5L0fmmVZ4mPe6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; zh-CN) WindowsPowerShell/5.1.19041.6328', 'eyJfdG9rZW4iOiJrcHdCRDFHQjZWQkpKRUpvTklMNFJoNkNFckJMWE1WZDMyVE9tQnh6IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9iYWNrZW5kXC9sb2dpbiIsInJvdXRlIjoiYmFja2VuZC5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX2JhY2tlbmRfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6OTM0MDM1ODAyNTU0NTc2ODk3fQ==', 1784894484);
INSERT INTO `sessions` VALUES ('LxHpydyPgfrIdSkmzThC5yRjvB9VfBkh00cJH61J', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; zh-CN) WindowsPowerShell/5.1.19041.6328', 'eyJfdG9rZW4iOiJQcEp2ejNhWVFqWU9WN1VqTXUwbVdKdnI1NlZWZndjUk5meW1sZFI1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9iYWNrZW5kXC9hcGlcL2NhdGVnb3JpZXMiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1784891370);
INSERT INTO `sessions` VALUES ('NF8RCbmY4H9fvobQzNIwbxUhbnyAf9115BrGfA0O', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'eyJfdG9rZW4iOiJFT2tEajFYOWR1V0xsOUplRzlRVWFUUVZsZVViQXNHczhEUTRZc0g3IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9iYWNrZW5kXC9tZW51cyIsInJvdXRlIjoiYmFja2VuZC5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX2JhY2tlbmRfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6OTM0MDM1ODAyNTU0NTc2ODk3fQ==', 1784894319);
INSERT INTO `sessions` VALUES ('Q6oquW6WR1qxIMfGTObOeT9ULCG5YQFej4JO3FC5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.1.15 Chrome/142.0.7444.265 Electron/39.8.1 Safari/537.36', 'eyJfdG9rZW4iOiJVUUNBTUZRVURZMXFSRHZUTk1NNTF1QnBzTktMNzl0UDdqNnpXbEthIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9iYWNrZW5kXC9uZXdzXC9jYXRlZ29yaWVzIiwicm91dGUiOiJiYWNrZW5kLmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1784891412);
INSERT INTO `sessions` VALUES ('qiyqvNIFDcVyrzsS3v3VY4X8aFZz7jBw2olSxeXx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; zh-CN) WindowsPowerShell/5.1.19041.6328', 'eyJfdG9rZW4iOiJlWkxvMmYyRkR1NzdyTWplSGhBcXY0V21ySGtya3RETzJGZ1loNjFCIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2JhY2tlbmRcL2FwaVwvYXV0aFwvbWUiLCJyb3V0ZSI6bnVsbH19', 1784892147);
INSERT INTO `sessions` VALUES ('qmczJ96sLAWr3HiTvyWCf84l9pqifWgzvcOpIWH5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; zh-CN) WindowsPowerShell/5.1.19041.6328', 'eyJfdG9rZW4iOiJFTm03VTd5SWw4bEFzU3ltbWwwWlhTWTU0SUY2QWJZdk03UnpVejk2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9iYWNrZW5kXC9uZXdzXC9jYXRlZ29yaWVzIiwicm91dGUiOiJiYWNrZW5kLmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1784891387);
INSERT INTO `sessions` VALUES ('VucumqsxJgo6kjkhyUruT6hGGeVbqAsMuaspJSlz', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; zh-CN) WindowsPowerShell/5.1.19041.6328', 'eyJfdG9rZW4iOiJKNU94c25TNUtIdDNmSlBjczA3TVIyVmJHbmNwZ2x4VFhjWEtHaURrIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9iYWNrZW5kXC9sb2dpbiIsInJvdXRlIjoiYmFja2VuZC5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX2JhY2tlbmRfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6OTM0MDM1ODAyNTU0NTc2ODk3fQ==', 1784894103);
INSERT INTO `sessions` VALUES ('We7UVPlzi3nNQwaWvc3Sxz1AG3xmyclJ3A8XUhQu', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; zh-CN) WindowsPowerShell/5.1.19041.6328', 'eyJfdG9rZW4iOiJwbjZBRXR6NURKeVNvWDQzZ1VXTk1XU1hrMkpscmp2bnE3VUpnUGM4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9iYWNrZW5kXC9sb2dpbiIsInJvdXRlIjoiYmFja2VuZC5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX2JhY2tlbmRfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6OTM0MDM1ODAyNTU0NTc2ODk3fQ==', 1784892248);

-- ----------------------------
-- Table structure for user_account
-- ----------------------------
DROP TABLE IF EXISTS `user_account`;
CREATE TABLE `user_account`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户唯一主键ID（雪花ID，不自增，分布式安全）',
  `user_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '账号用户名，唯一，可用于登录',
  `nick_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户昵称',
  `user_mobile` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '手机号，唯一索引，登录首选',
  `user_email` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮箱，唯一索引，找回密码',
  `password_hash` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'BCrypt/Argon2加密密码，禁止明文存储',
  `password_salt` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '自定义盐值（BCrypt自带盐可留空）',
  `user_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '账号状态：0-禁用 1-正常 2-冻结 3-注销',
  `lock_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '冻结/封禁原因（风控、违规、人工封禁）',
  `lock_expire_time` datetime NULL DEFAULT NULL COMMENT '限时冻结到期时间，NULL=永久封禁',
  `last_login_ip` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_region` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'IP归属地',
  `last_login_at` datetime NULL DEFAULT NULL COMMENT '最后登录时间',
  `register_ip` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '注册IP',
  `register_device` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '注册设备标识',
  `register_channel` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web' COMMENT '注册渠道：web/app/mini/ios/android',
  `real_auth_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '实名状态：0未实名 1待审核 2已实名 3实名驳回',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime NULL DEFAULT NULL COMMENT '删除时间（软删除记录）',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_username`(`user_name` ASC) USING BTREE,
  UNIQUE INDEX `uk_mobile`(`user_mobile` ASC) USING BTREE,
  UNIQUE INDEX `uk_email`(`user_email` ASC) USING BTREE,
  INDEX `idx_status_auth`(`user_status` ASC, `real_auth_status` ASC) USING BTREE,
  INDEX `idx_deleted_time`(`created_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 934035802554576899 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '用户账号主表｜登录、安全、状态核心数据' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of user_account
-- ----------------------------
INSERT INTO `user_account` VALUES (920733860755423001, 'admin123', '', '13800000001', 'admin@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', 1, '', NULL, '192.168.1.100', '广东深圳', '2026-07-23 09:00:00', '192.168.1.1', 'Chrome/Windows', 'web', 2, '2026-01-01 00:00:00', '2026-07-23 09:00:00', NULL);
INSERT INTO `user_account` VALUES (920733860755423002, 'super_admin', '', '13800000002', 'super@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', 1, '', NULL, '192.168.1.101', '广东深圳', '2026-07-22 18:00:00', '192.168.1.1', 'Chrome/Mac', 'web', 2, '2026-01-02 00:00:00', '2026-07-22 18:00:00', NULL);
INSERT INTO `user_account` VALUES (920733860755423003, 'editor_zhang', '', '13800000003', 'zhangwei@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', 1, '123456', NULL, '192.168.1.102', '广东广州', '2026-07-23 10:00:00', '192.168.1.2', 'Safari/iPhone', 'app', 2, '2026-02-01 00:00:00', '2026-07-23 09:27:04', NULL);
INSERT INTO `user_account` VALUES (920733860755423004, 'ops_li', '', '13800000004', 'liming@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', 1, '123456', NULL, '10.0.0.5', '广东深圳', '2026-07-22 16:00:00', '10.0.0.1', 'Chrome/Windows', 'web', 2, '2026-03-01 00:00:00', '2026-07-23 09:27:05', NULL);
INSERT INTO `user_account` VALUES (920733860755423005, 'pm_wang', '', '13800000005', 'wangfang@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', 1, '123456', NULL, '172.16.0.10', '上海浦东', '2026-07-21 14:00:00', '172.16.0.1', 'Edge/Windows', 'mini', 1, '2026-04-01 00:00:00', '2026-07-23 09:27:05', NULL);
INSERT INTO `user_account` VALUES (920733860755423006, 'sales_chen', '', '13800000006', 'chenjun@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', 1, '123456', NULL, '192.168.2.15', '北京朝阳', '2026-07-20 11:00:00', '192.168.2.1', 'Chrome/Mac', 'ios', 0, '2026-05-01 00:00:00', '2026-07-23 09:27:06', NULL);
INSERT INTO `user_account` VALUES (920733860755423007, 'finance_lin', '', '13800000007', 'linna@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', 1, '123456', NULL, '10.0.1.20', '广东深圳', '2026-07-19 09:30:00', '10.0.1.1', 'Firefox/Windows', 'web', 1, '2026-06-01 00:00:00', '2026-07-23 09:27:06', NULL);
INSERT INTO `user_account` VALUES (920733860755423008, 'intern_huang', '', '13800000008', 'huangxiao@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', 1, '123456', NULL, '192.168.3.30', '广东广州', '2026-07-18 08:00:00', '192.168.3.1', 'Safari/iPhone', 'app', 0, '2026-07-01 00:00:00', '2026-07-23 09:27:08', NULL);
INSERT INTO `user_account` VALUES (920733860755423009, 'former_zhao', '', '13800000009', 'zhaolei@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', 2, '因违反公司信息安全规定，账号临时冻结', '2026-08-31 23:59:59', '192.168.4.50', '广东深圳', '2026-07-10 15:00:00', '192.168.4.1', 'Chrome/Windows', 'web', 2, '2026-01-15 00:00:00', '2026-07-10 15:00:00', NULL);
INSERT INTO `user_account` VALUES (920733860755423010, 'former_lu', '', '13800000010', 'luyang@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', 3, '用户主动注销', NULL, '192.168.5.60', '浙江杭州', '2026-06-01 10:00:00', '192.168.5.1', 'Chrome/Mac', 'web', 2, '2025-12-01 00:00:00', '2026-06-01 10:00:00', '2026-06-01 10:00:00');
INSERT INTO `user_account` VALUES (934035802554576896, 'sunny', '', '13026661119', 'itpeeg@gmail.com', '$2y$10$RTRidRM2EtMIH6pAhsula..8xqM84yh9CPqN3/5pX5JpKP9vscA9e', 'salt', 1, '', NULL, '', '', NULL, '', '', 'web', 0, '2026-07-22 02:39:20', '2026-07-23 04:50:10', NULL);
INSERT INTO `user_account` VALUES (934035802554576897, 'admin', '管理员', '13800000000', 'admin@example.com', '$2y$12$HhM3Cq8aFGc0xhkMh0kneuMQZxTGW583MSAKN4/F255Nfu1Tr4a4K', '', 1, '', NULL, '127.0.0.1', '', '2026-07-24 12:01:24', '', '', 'web', 2, '2026-07-23 02:21:16', '2026-07-24 12:01:24', NULL);
INSERT INTO `user_account` VALUES (934035802554576898, 'testuser', '', '13800138002', 'test@example.com', '$2y$10$RTRidRM2EtMIH6pAhsula..8xqM84yh9CPqN3/5pX5JpKP9vscA9e', 'salt', 1, '', NULL, '', '', NULL, '', '', 'web', 1, '2026-07-23 02:21:47', '2026-07-23 04:50:13', NULL);

SET FOREIGN_KEY_CHECKS = 1;
