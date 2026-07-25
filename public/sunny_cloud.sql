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

 Date: 25/07/2026 19:27:09
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
) ENGINE = InnoDB AUTO_INCREMENT = 920733863034403281 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '菜单/功能表' ROW_FORMAT = DYNAMIC;

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
INSERT INTO `auth_menus` VALUES (920733863034403266, 0, '人事管理', 'Avatar', '', '', 'hr', 65, 1, '2026-07-25 10:03:56', '2026-07-25 10:03:56', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403267, 920733863034403266, '部门管理', '', '/backend/hr/departments', 'hr/Department', 'hr.department', 30, 1, '2026-07-25 10:03:56', '2026-07-25 10:03:56', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403268, 920733863034403266, '岗位管理', '', '/backend/hr/posts', 'hr/Post', 'hr.post', 20, 1, '2026-07-25 10:03:56', '2026-07-25 10:03:56', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403269, 920733863034403266, '任职管理', '', '/backend/hr/user-dept-posts', 'hr/UserDeptPost', 'hr.user_dept_post', 10, 1, '2026-07-25 10:03:56', '2026-07-25 10:03:56', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403270, 0, '商品管理', 'Goods', '', '', 'products', 40, 1, '2026-07-25 10:29:43', '2026-07-25 10:30:49', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403271, 920733863034403270, '品牌管理', '', '/backend/product/brands', 'product/Brand', 'product.brand', 40, 1, '2026-07-25 10:29:43', '2026-07-25 10:29:43', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403272, 920733863034403270, '商品分类', '', '/backend/product/categories', 'product/Category', 'product.category', 30, 1, '2026-07-25 10:29:43', '2026-07-25 10:29:43', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403273, 920733863034403270, '规格管理', '', '/backend/product/specifications', 'product/Specification', 'product.spec', 20, 1, '2026-07-25 10:29:43', '2026-07-25 10:29:43', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403274, 920733863034403270, '商品管理', '', '/backend/product/products', 'product/Index', 'product.product', 50, 1, '2026-07-25 10:37:40', '2026-07-25 10:37:40', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403275, 0, '流程管理', 'Share', '', '', 'wf', 35, 1, '2026-07-25 11:18:13', '2026-07-25 11:18:13', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403276, 920733863034403275, '流程类型', '', '/backend/wf/flow-types', 'wf/FlowType', 'wf.flow_type', 20, 1, '2026-07-25 11:18:13', '2026-07-25 11:18:13', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403277, 920733863034403275, '流程模板', '', '/backend/wf/flow-definitions', 'wf/FlowDefinitionIndex', 'wf.flow_definition', 10, 1, '2026-07-25 11:18:13', '2026-07-25 11:18:13', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403278, 920733863034403275, '待我审批', '', '/backend/wf/todo', 'wf/TodoIndex', 'wf.todo', 50, 1, '2026-07-25 11:25:17', '2026-07-25 11:25:17', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403279, 920733863034403275, '我的申请', '', '/backend/wf/applies', 'wf/ApplyIndex', 'wf.apply', 40, 1, '2026-07-25 11:25:17', '2026-07-25 11:25:17', NULL);
INSERT INTO `auth_menus` VALUES (920733863034403280, 920733863034403275, '抄送我的', '', '/backend/wf/cc', 'wf/CcIndex', 'wf.cc', 30, 1, '2026-07-25 11:25:17', '2026-07-25 11:25:17', NULL);

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
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423248, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423249, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423250, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423251, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423252, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423253, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423254, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423255, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423256, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423257, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423258, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423259, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423260, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423261, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423262, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423263, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423264, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423265, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423266, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423267, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423268, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423269, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423270, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423271, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423272, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423273, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733860755423274, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403263, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403264, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403265, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403266, '2026-07-25 10:03:56');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403267, '2026-07-25 10:03:56');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403268, '2026-07-25 10:03:56');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403269, '2026-07-25 10:03:56');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403270, '2026-07-25 10:37:40');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403271, '2026-07-25 10:37:40');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403272, '2026-07-25 10:37:40');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403273, '2026-07-25 10:37:40');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403274, '2026-07-25 10:37:40');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403275, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403276, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403277, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403278, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403279, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` VALUES (920733860755423247, 920733863034403280, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423248, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423249, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423250, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423251, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423252, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423253, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423254, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423255, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423256, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423257, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423258, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423259, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423260, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423261, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423262, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423263, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423264, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423265, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423266, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423267, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423268, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423269, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423270, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423271, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423272, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423273, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733860755423274, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403263, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403264, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403265, '2026-07-24 12:06:27');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403266, '2026-07-25 10:03:56');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403267, '2026-07-25 10:03:56');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403268, '2026-07-25 10:03:56');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403269, '2026-07-25 10:03:56');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403270, '2026-07-25 10:37:40');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403271, '2026-07-25 10:37:40');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403272, '2026-07-25 10:37:40');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403273, '2026-07-25 10:37:40');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403274, '2026-07-25 10:37:40');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403275, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403276, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403277, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403278, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403279, '2026-07-25 11:25:17');
INSERT INTO `auth_role_menus` VALUES (920733860755423257, 920733863034403280, '2026-07-25 11:25:17');

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
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423246, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423247, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423248, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423249, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423250, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423251, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423252, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423253, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423254, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423255, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423256, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423257, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423258, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423259, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423260, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423261, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423262, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423263, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423264, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423265, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423266, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423267, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423268, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423269, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423270, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423271, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423272, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423273, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423274, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423275, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423276, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423277, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423278, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423279, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423280, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423281, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423282, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423283, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423284, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423285, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423286, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423287, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423288, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423289, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423290, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423291, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423247, 920733862755423292, '2026-07-24 12:06:27');
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
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423246, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423247, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423248, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423249, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423250, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423251, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423252, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423253, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423254, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423255, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423256, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423257, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423258, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423259, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423260, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423261, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423262, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423263, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423264, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423265, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423266, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423267, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423268, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423269, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423270, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423271, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423272, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423273, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423274, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423275, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423276, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423277, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423278, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423279, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423280, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423281, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423282, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423283, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423284, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423285, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423286, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423287, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423288, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423289, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423290, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423291, '2026-07-24 12:06:27');
INSERT INTO `auth_role_permissions` VALUES (920733860755423257, 920733862755423292, '2026-07-24 12:06:27');

-- ----------------------------
-- Table structure for auth_user_role
-- ----------------------------
DROP TABLE IF EXISTS `auth_user_role`;
CREATE TABLE `auth_user_role`  (
  `user_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `role_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`user_id`, `role_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '用户-角色关联' ROW_FORMAT = DYNAMIC;

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
INSERT INTO `auth_user_role` VALUES (934035802554576897, 920733860755423247, '2026-07-24 12:06:36');

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for hr_department
-- ----------------------------
DROP TABLE IF EXISTS `hr_department`;
CREATE TABLE `hr_department`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '部门主键ID',
  `parent_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '父部门ID，0=根节点（总公司）',
  `dept_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '部门名称：总公司、深圳分公司、技术部、财务部、商品运营组',
  `dept_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '部门唯一编码，程序权限/审批规则使用',
  `ancestors` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '祖先ID路径，逗号分隔：0,1,5，用于快速查上级/所有下级，冗余字段提升性能',
  `dept_level` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '层级深度：1根节点、2一级部门、3二级小组',
  `leader_user_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '部门负责人ID（关联sys_user.id），审批可直接取部门负责人',
  `dept_phone` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '部门联系电话',
  `dept_sort` int NOT NULL DEFAULT 0 COMMENT '树形展示排序号',
  `dept_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 0禁用 1正常启用',
  `created_by` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人用户ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_dept_code`(`dept_code` ASC) USING BTREE,
  INDEX `idx_parent_id`(`parent_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733862005400006 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '企业组织架构部门表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hr_department
-- ----------------------------
INSERT INTO `hr_department` VALUES (920733862005400000, 0, '集团总公司', 'group_all', '0', 1, 0, '', 1, 1, 0, '2026-07-25 02:19:47', '2026-07-25 02:19:47', NULL);
INSERT INTO `hr_department` VALUES (920733862005400001, 920733862005400000, '深圳分公司', 'branch_shenzhen', '0,920733862005400000', 2, 10001, '', 1, 1, 0, '2026-07-25 02:19:47', '2026-07-25 02:21:53', NULL);
INSERT INTO `hr_department` VALUES (920733862005400002, 920733862005400001, '财务部', 'dept_finance', '0,920733862005400000,920733862005400001', 3, 10002, '', 1, 1, 0, '2026-07-25 02:19:47', '2026-07-25 02:22:23', NULL);
INSERT INTO `hr_department` VALUES (920733862005400003, 920733862005400001, '运营部', 'dept_operation', '0,920733862005400000,920733862005400002', 3, 10003, '', 2, 1, 0, '2026-07-25 02:19:47', '2026-07-25 02:22:35', NULL);
INSERT INTO `hr_department` VALUES (920733862005400004, 920733862005400003, '商品运营小组', 'group_product', '0,920733862005400000,920733862005400002,920733862005400003', 4, 10004, '', 1, 1, 0, '2026-07-25 02:19:47', '2026-07-25 02:22:51', NULL);
INSERT INTO `hr_department` VALUES (920733862005400005, 920733862005400001, '技术部', 'dept_it', '0,920733862005400000,920733862005400001', 1, 0, '', 0, 1, 0, '2026-07-25 03:09:02', '2026-07-25 03:09:25', NULL);

-- ----------------------------
-- Table structure for hr_dept_leaders
-- ----------------------------
DROP TABLE IF EXISTS `hr_dept_leaders`;
CREATE TABLE `hr_dept_leaders`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `dept_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '组织ID',
  `user_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '负责人ID',
  `role_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '负责人类型：1主要负责人，2次要负责人',
  `created_at` datetime NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '更新人',
  `deleted_at` datetime NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `index_dept_id`(`dept_id` ASC) USING BTREE,
  INDEX `index_user_id`(`user_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733860747034689 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '部门负责人表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hr_dept_leaders
-- ----------------------------

-- ----------------------------
-- Table structure for hr_post
-- ----------------------------
DROP TABLE IF EXISTS `hr_post`;
CREATE TABLE `hr_post`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '岗位主键ID',
  `parent_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级岗位ID，0=顶级根岗位',
  `post_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '岗位名称：总经理、部门经理、技术主管、前端开发、财务专员',
  `post_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '岗位唯一编码（用于代码判断、审批规则）',
  `post_sort` int NOT NULL DEFAULT 0 COMMENT '排序号',
  `post_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 0=禁用 1=正常启用',
  `remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '岗位描述备注',
  `created_by` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人用户ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_post_code`(`post_code` ASC) USING BTREE,
  INDEX `idx_parent_id`(`parent_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733862004423256 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '人事岗位表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hr_post
-- ----------------------------
INSERT INTO `hr_post` VALUES (920733862004423246, 0, '总经理', 'general_manager', 1, 1, '公司最高负责人，终审审批', 0, '2026-07-25 02:10:13', '2026-07-25 02:10:13', NULL);
INSERT INTO `hr_post` VALUES (920733862004423247, 920733862004423246, '部门经理', 'dept_manager', 2, 1, '各部门负责人，一级审批', 0, '2026-07-25 02:10:13', '2026-07-25 02:11:03', NULL);
INSERT INTO `hr_post` VALUES (920733862004423248, 920733862004423247, '技术主管', 'tech_supervisor', 3, 1, '技术团队管理', 0, '2026-07-25 02:10:13', '2026-07-25 02:11:03', NULL);
INSERT INTO `hr_post` VALUES (920733862004423249, 920733862004423247, '财务专员', 'finance_staff', 4, 1, '费用报销、付款审核', 0, '2026-07-25 02:10:13', '2026-07-25 02:11:04', NULL);
INSERT INTO `hr_post` VALUES (920733862004423250, 920733862004423247, '采购专员', 'purchase_staff', 5, 1, '采购申请发起与初审', 0, '2026-07-25 02:10:13', '2026-07-25 02:11:04', NULL);
INSERT INTO `hr_post` VALUES (920733862004423251, 920733862004423247, '运营专员', 'operation_staff', 6, 1, '商品上架、客户入驻初审', 0, '2026-07-25 02:10:13', '2026-07-25 02:11:05', NULL);
INSERT INTO `hr_post` VALUES (920733862004423252, 920733862004423248, '后端工程师', 'back', 7, 1, '', 0, '2026-07-25 02:12:05', '2026-07-25 02:13:02', NULL);
INSERT INTO `hr_post` VALUES (920733862004423255, 920733862004423248, '前端工程师', 'front', 8, 1, '', 0, '2026-07-25 02:12:51', '2026-07-25 02:13:02', NULL);

-- ----------------------------
-- Table structure for hr_user_dept_post
-- ----------------------------
DROP TABLE IF EXISTS `hr_user_dept_post`;
CREATE TABLE `hr_user_dept_post`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '员工ID，关联sys_user.id',
  `dept_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属部门ID，关联sys_department.id',
  `post_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '兼任岗位ID，关联sys_post.id',
  `is_main` tinyint NOT NULL DEFAULT 0 COMMENT '是否为主岗位/主部门 0=兼职 1=本职主岗',
  `remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '兼任说明-同一个员工不能在同一个部门重复挂同一个岗位',
  `start_at` datetime NULL DEFAULT NULL COMMENT '任职开始日期',
  `end_at` datetime NULL DEFAULT NULL COMMENT '任职结束日期，离职/调岗则填充',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_user_dept_post`(`user_id` ASC, `dept_id` ASC, `post_id` ASC) USING BTREE,
  INDEX `idx_user_id`(`user_id` ASC) USING BTREE,
  INDEX `idx_dept_id`(`dept_id` ASC) USING BTREE,
  INDEX `idx_post_id`(`post_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '员工-部门-岗位多兼任关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hr_user_dept_post
-- ----------------------------
INSERT INTO `hr_user_dept_post` VALUES (1, 934035802554576896, 920733862005400001, 920733862004423252, 1, '', '2026-07-01 00:00:00', NULL, '2026-07-25 03:07:33', '2026-07-25 03:10:46');
INSERT INTO `hr_user_dept_post` VALUES (2, 934035802554576898, 920733862005400001, 920733862004423255, 1, '', '2026-05-01 00:00:00', NULL, '2026-07-25 03:07:33', '2026-07-25 03:10:20');

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 30 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

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
INSERT INTO `migrations` VALUES (9, '2026_07_24_000006_create_auth_relation_tables', 999);
INSERT INTO `migrations` VALUES (10, '2026_07_25_000001_create_hr_department_table', 1000);
INSERT INTO `migrations` VALUES (11, '2026_07_25_000002_create_hr_post_table', 1000);
INSERT INTO `migrations` VALUES (12, '2026_07_25_000003_create_hr_dept_leaders_table', 1000);
INSERT INTO `migrations` VALUES (13, '2026_07_25_000004_create_hr_user_dept_post_table', 1000);
INSERT INTO `migrations` VALUES (14, '2026_07_25_000010_create_product_brand_table', 1001);
INSERT INTO `migrations` VALUES (15, '2026_07_25_000011_create_product_category_table', 1001);
INSERT INTO `migrations` VALUES (16, '2026_07_25_000012_create_product_specification_table', 1001);
INSERT INTO `migrations` VALUES (17, '2026_07_25_000013_create_product_specification_value_table', 1001);
INSERT INTO `migrations` VALUES (18, '2026_07_25_000020_create_product_table', 1002);
INSERT INTO `migrations` VALUES (19, '2026_07_25_000021_create_product_media_table', 1002);
INSERT INTO `migrations` VALUES (20, '2026_07_25_000022_create_product_sku_table', 1002);
INSERT INTO `migrations` VALUES (21, '2026_07_25_000023_create_product_sku_spec_value_table', 1002);
INSERT INTO `migrations` VALUES (22, '2026_07_25_000030_create_wf_flow_type_table', 1003);
INSERT INTO `migrations` VALUES (23, '2026_07_25_000031_create_wf_flow_definition_table', 1003);
INSERT INTO `migrations` VALUES (24, '2026_07_25_000032_create_wf_flow_form_table', 1003);
INSERT INTO `migrations` VALUES (25, '2026_07_25_000033_create_wf_flow_node_table', 1003);
INSERT INTO `migrations` VALUES (26, '2026_07_25_000034_create_wf_flow_node_condition_table', 1004);
INSERT INTO `migrations` VALUES (27, '2026_07_25_000035_create_wf_flow_apply_table', 1004);
INSERT INTO `migrations` VALUES (28, '2026_07_25_000036_create_wf_flow_approve_record_table', 1004);
INSERT INTO `migrations` VALUES (29, '2026_07_25_000037_create_wf_flow_cc_user_table', 1004);

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
-- Table structure for product
-- ----------------------------
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `auto_code` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '系统产生编码SP000001自增',
  `product_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品名称',
  `product_model` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品型号',
  `category_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品分类ID',
  `brand_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '品牌ID',
  `material_quality` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '材质',
  `filling` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '填充',
  `short_desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '商品简短描述',
  `main_image_url` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '主图URL',
  `product_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 0=已下架 1=已上架',
  `sort_order` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `created_at` datetime(6) NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `updated_at` datetime(6) NULL DEFAULT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `deleted_at` datetime(6) NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `product_category_id_index`(`category_id` ASC) USING BTREE,
  INDEX `product_brand_id_index`(`brand_id` ASC) USING BTREE,
  INDEX `product_auto_code_index`(`auto_code` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733863000000001 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product
-- ----------------------------
INSERT INTO `product` VALUES (920733863000000000, 'SP000001', '测试的', 'SFF234324234', 920733862755420000, 920733862755000000, '木头的', '海绵的', '完成产品主表，产品图片暂时就用本地存储，在网站创建文件夹目录存储\n完成产品sku模块。', '/uploads/products/2026/07/01kycdx3a2t5sanh1vjf33jjzc.png', 1, 0, '2026-07-25 10:42:43.000000', 934035802554576897, '2026-07-25 10:42:43.000000', 934035802554576897, NULL, NULL);

-- ----------------------------
-- Table structure for product_brand
-- ----------------------------
DROP TABLE IF EXISTS `product_brand`;
CREATE TABLE `product_brand`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `brand_code` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '系统产生编码BR000001自增',
  `brand_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '品牌名称',
  `alias` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '英文别名（可选）',
  `is_system` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否系统预设 1=系统预设 0=自定义',
  `is_show` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 0=隐藏 1=显示',
  `sort_order` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `brand_remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` datetime(6) NULL DEFAULT NULL COMMENT '创建时间',
  `created_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '创建人',
  `updated_at` datetime(6) NULL DEFAULT NULL COMMENT '更新时间',
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '更新人',
  `deleted_at` datetime(6) NULL DEFAULT NULL COMMENT '删除时间',
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '删除人',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733862755000002 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_brand
-- ----------------------------
INSERT INTO `product_brand` VALUES (920733862755000000, 'BR000001', '自有品牌', 'Own', 1, 1, 100, '系统预设品牌', '2026-07-25 10:29:42.000000', NULL, '2026-07-25 10:29:42.000000', NULL, NULL, NULL);
INSERT INTO `product_brand` VALUES (920733862755000001, 'BR000002', '示例品牌', 'Demo', 0, 1, 90, '', '2026-07-25 10:29:42.000000', NULL, '2026-07-25 10:29:42.000000', NULL, NULL, NULL);

-- ----------------------------
-- Table structure for product_category
-- ----------------------------
DROP TABLE IF EXISTS `product_category`;
CREATE TABLE `product_category`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `category_code` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '系统产生编码FL000001自增',
  `category_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `parent_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级分类ID 0是一级分类',
  `level` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '级别 1=一级 2=二级 3=三级',
  `product_count` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品数量 冗余',
  `unit` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '数量单位',
  `cat_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 0=隐藏 1=显示',
  `sort_order` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `cat_remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` datetime(6) NULL DEFAULT NULL COMMENT '创建时间',
  `created_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '创建人',
  `updated_at` datetime(6) NULL DEFAULT NULL COMMENT '更新时间',
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '更新人',
  `deleted_at` datetime(6) NULL DEFAULT NULL COMMENT '删除时间',
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '删除人',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `category_parent_id_index`(`parent_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733862755420003 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_category
-- ----------------------------
INSERT INTO `product_category` VALUES (920733862755420000, 'FL000001', '家具', 0, 1, 1, '件', 1, 100, '', '2026-07-25 10:29:42.000000', NULL, '2026-07-25 10:42:45.000000', NULL, NULL, NULL);
INSERT INTO `product_category` VALUES (920733862755420001, 'FL000002', '沙发', 920733862755420000, 2, 0, '件', 1, 20, '', '2026-07-25 10:29:42.000000', NULL, '2026-07-25 10:29:42.000000', NULL, NULL, NULL);
INSERT INTO `product_category` VALUES (920733862755420002, 'FL000003', '桌椅', 920733862755420000, 2, 0, '套', 1, 10, '', '2026-07-25 10:29:42.000000', NULL, '2026-07-25 10:29:42.000000', NULL, NULL, NULL);

-- ----------------------------
-- Table structure for product_media
-- ----------------------------
DROP TABLE IF EXISTS `product_media`;
CREATE TABLE `product_media`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `product_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品ID',
  `media_type` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '类型 1=主图 2=详情图 3=视频 4=资质文件 5=其他附件',
  `file_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '文件URL',
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '原始文件名',
  `file_key` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '存储键/路径',
  `storage_provider` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local' COMMENT '存储提供方',
  `extension` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件拓展名',
  `file_size` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '字节大小',
  `file_type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'MimeType',
  `sort_order` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `created_at` datetime(6) NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `updated_at` datetime(6) NULL DEFAULT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `deleted_at` datetime(6) NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `media_product_id_index`(`product_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733863100000002 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_media
-- ----------------------------
INSERT INTO `product_media` VALUES (920733863100000000, 920733863000000000, 1, '/uploads/products/2026/07/01kycdx3a2t5sanh1vjf33jjzc.png', 'gywm_logo.png', 'uploads/products/2026/07/01kycdx3a2t5sanh1vjf33jjzc.png', 'local', 'png', 4418, 'image/png', 0, '2026-07-25 10:42:45.000000', 934035802554576897, '2026-07-25 10:42:45.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_media` VALUES (920733863100000001, 920733863000000000, 2, '/uploads/products/2026/07/01kycdx90zj2vpppkafdd0fkaa.png', '设计方案.png', 'uploads/products/2026/07/01kycdx90zj2vpppkafdd0fkaa.png', 'local', 'png', 1581757, 'image/png', 1, '2026-07-25 10:42:45.000000', 934035802554576897, '2026-07-25 10:42:45.000000', 934035802554576897, NULL, NULL);

-- ----------------------------
-- Table structure for product_sku
-- ----------------------------
DROP TABLE IF EXISTS `product_sku`;
CREATE TABLE `product_sku`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `product_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品ID',
  `sku_code` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'SKU编码',
  `price` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '销售价',
  `market_price` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '划线价/市场价',
  `cost_price` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '成本价',
  `stock_num` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '库存数量',
  `weight` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '重量(KG)',
  `volume` decimal(10, 4) UNSIGNED NOT NULL DEFAULT 0.0000 COMMENT '体积(m³)',
  `sale_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '销售状态 0下架 1上架',
  `sort_order` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `created_at` datetime(6) NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `updated_at` datetime(6) NULL DEFAULT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `deleted_at` datetime(6) NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sku_code_unique`(`sku_code` ASC) USING BTREE,
  INDEX `sku_product_id_index`(`product_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733863200000006 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_sku
-- ----------------------------
INSERT INTO `product_sku` VALUES (920733863200000000, 920733863000000000, 'SKU000001', 10.00, 20.00, 0.00, 0, 10.00, 0.1000, 1, 0, '2026-07-25 10:42:43.000000', 934035802554576897, '2026-07-25 10:42:43.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku` VALUES (920733863200000001, 920733863000000000, 'SKU000002', 20.00, 30.00, 0.00, 0, 20.00, 0.1000, 1, 1, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku` VALUES (920733863200000002, 920733863000000000, 'SKU000003', 30.00, 40.00, 0.00, 0, 30.00, 1.0000, 1, 2, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku` VALUES (920733863200000003, 920733863000000000, 'SKU000004', 40.00, 50.00, 0.00, 0, 40.00, 0.1000, 1, 3, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku` VALUES (920733863200000004, 920733863000000000, 'SKU000005', 50.00, 60.00, 0.00, 0, 50.00, 0.1000, 1, 4, '2026-07-25 10:42:45.000000', 934035802554576897, '2026-07-25 10:42:45.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku` VALUES (920733863200000005, 920733863000000000, 'SKU000006', 60.00, 70.00, 0.00, 0, 60.00, 0.0100, 1, 5, '2026-07-25 10:42:45.000000', 934035802554576897, '2026-07-25 10:42:45.000000', 934035802554576897, NULL, NULL);

-- ----------------------------
-- Table structure for product_sku_spec_value
-- ----------------------------
DROP TABLE IF EXISTS `product_sku_spec_value`;
CREATE TABLE `product_sku_spec_value`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `sku_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联SKU表ID',
  `spec_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联规格维度ID',
  `spec_value_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联规格值ID',
  `created_at` datetime(6) NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `updated_at` datetime(6) NULL DEFAULT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `deleted_at` datetime(6) NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sku_spec_value_unique`(`sku_id` ASC, `spec_id` ASC, `spec_value_id` ASC) USING BTREE,
  INDEX `sku_spec_sku_id_index`(`sku_id` ASC) USING BTREE,
  INDEX `sku_spec_value_id_index`(`spec_value_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733863300000012 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_sku_spec_value
-- ----------------------------
INSERT INTO `product_sku_spec_value` VALUES (920733863300000000, 920733863200000000, 920733862755400000, 920733862755320000, '2026-07-25 10:42:43.000000', 934035802554576897, '2026-07-25 10:42:43.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` VALUES (920733863300000001, 920733863200000000, 920733862755400001, 920733862755320003, '2026-07-25 10:42:43.000000', 934035802554576897, '2026-07-25 10:42:43.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` VALUES (920733863300000002, 920733863200000001, 920733862755400000, 920733862755320000, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` VALUES (920733863300000003, 920733863200000001, 920733862755400001, 920733862755320004, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` VALUES (920733863300000004, 920733863200000002, 920733862755400000, 920733862755320001, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` VALUES (920733863300000005, 920733863200000002, 920733862755400001, 920733862755320003, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` VALUES (920733863300000006, 920733863200000003, 920733862755400000, 920733862755320001, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` VALUES (920733863300000007, 920733863200000003, 920733862755400001, 920733862755320004, '2026-07-25 10:42:44.000000', 934035802554576897, '2026-07-25 10:42:44.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` VALUES (920733863300000008, 920733863200000004, 920733862755400000, 920733862755320002, '2026-07-25 10:42:45.000000', 934035802554576897, '2026-07-25 10:42:45.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` VALUES (920733863300000009, 920733863200000004, 920733862755400001, 920733862755320003, '2026-07-25 10:42:45.000000', 934035802554576897, '2026-07-25 10:42:45.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` VALUES (920733863300000010, 920733863200000005, 920733862755400000, 920733862755320002, '2026-07-25 10:42:45.000000', 934035802554576897, '2026-07-25 10:42:45.000000', 934035802554576897, NULL, NULL);
INSERT INTO `product_sku_spec_value` VALUES (920733863300000011, 920733863200000005, 920733862755400001, 920733862755320004, '2026-07-25 10:42:45.000000', 934035802554576897, '2026-07-25 10:42:45.000000', 934035802554576897, NULL, NULL);

-- ----------------------------
-- Table structure for product_specification
-- ----------------------------
DROP TABLE IF EXISTS `product_specification`;
CREATE TABLE `product_specification`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `spec_code` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '系统产生编码GL000001自增',
  `spec_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '规格名称',
  `spec_remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `spec_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 0=隐藏 1=显示',
  `sort_order` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `created_at` datetime(6) NULL DEFAULT NULL COMMENT '创建时间',
  `created_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '创建人',
  `updated_at` datetime(6) NULL DEFAULT NULL COMMENT '更新时间',
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '更新人',
  `deleted_at` datetime(6) NULL DEFAULT NULL COMMENT '删除时间',
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '删除人',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733862755400002 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_specification
-- ----------------------------
INSERT INTO `product_specification` VALUES (920733862755400000, 'GL000001', '颜色', '', 1, 100, '2026-07-25 10:29:43.000000', NULL, '2026-07-25 10:29:43.000000', NULL, NULL, NULL);
INSERT INTO `product_specification` VALUES (920733862755400001, 'GL000002', '材质', '', 1, 90, '2026-07-25 10:29:43.000000', NULL, '2026-07-25 10:29:43.000000', NULL, NULL, NULL);

-- ----------------------------
-- Table structure for product_specification_value
-- ----------------------------
DROP TABLE IF EXISTS `product_specification_value`;
CREATE TABLE `product_specification_value`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键(雪花ID)',
  `spec_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '规格ID',
  `value_code` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '系统产生编码GV000001自增',
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '规格值',
  `sort_order` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `value_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 0=隐藏 1=显示',
  `created_at` datetime(6) NULL DEFAULT NULL COMMENT '创建时间',
  `created_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '创建人',
  `updated_at` datetime(6) NULL DEFAULT NULL COMMENT '更新时间',
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '更新人',
  `deleted_at` datetime(6) NULL DEFAULT NULL COMMENT '删除时间',
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '删除人',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `spec_value_spec_id_index`(`spec_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733862755320005 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_specification_value
-- ----------------------------
INSERT INTO `product_specification_value` VALUES (920733862755320000, 920733862755400000, 'GV000001', '红色', 30, 1, '2026-07-25 10:29:43.000000', NULL, '2026-07-25 10:29:43.000000', NULL, NULL, NULL);
INSERT INTO `product_specification_value` VALUES (920733862755320001, 920733862755400000, 'GV000002', '黑色', 20, 1, '2026-07-25 10:29:43.000000', NULL, '2026-07-25 10:29:43.000000', NULL, NULL, NULL);
INSERT INTO `product_specification_value` VALUES (920733862755320002, 920733862755400000, 'GV000003', '白色', 10, 1, '2026-07-25 10:29:43.000000', NULL, '2026-07-25 10:29:43.000000', NULL, NULL, NULL);
INSERT INTO `product_specification_value` VALUES (920733862755320003, 920733862755400001, 'GV000004', '实木', 20, 1, '2026-07-25 10:29:43.000000', NULL, '2026-07-25 10:29:43.000000', NULL, NULL, NULL);
INSERT INTO `product_specification_value` VALUES (920733862755320004, 920733862755400001, 'GV000005', '布艺', 10, 1, '2026-07-25 10:29:43.000000', NULL, '2026-07-25 10:29:43.000000', NULL, NULL, NULL);

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
INSERT INTO `sessions` VALUES ('HYo1PkjmaPB3exeXjp9DDYC1qnmqL8qrVDEYvLEk', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJSVUF5QjNZMzBqVGVIb0daWERpNHhBR0ZNamsxZk81WG5XTmxMZVJTIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9iYWNrZW5kXC9tZW51cyIsInJvdXRlIjoiYmFja2VuZC5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX2JhY2tlbmRfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6OTM0MDM1ODAyNTU0NTc2ODk3fQ==', 1784978664);
INSERT INTO `sessions` VALUES ('m8mi3wRb8MIeAfEgY92zsYc3xGPZeFq8Xl1PvX25', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'eyJfdG9rZW4iOiJQbDlTZFdpdnVPTFdaRHJRbTdpaUNzUVNIM2FaOWtXUUtEbkpJbEc2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9iYWNrZW5kXC9sb2dpbiIsInJvdXRlIjoiYmFja2VuZC5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX2JhY2tlbmRfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6OTM0MDM1ODAyNTU0NTc2ODk3fQ==', 1784978701);

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
INSERT INTO `user_account` VALUES (934035802554576897, 'admin', '管理员', '13800000000', 'admin@example.com', '$2y$12$HhM3Cq8aFGc0xhkMh0kneuMQZxTGW583MSAKN4/F255Nfu1Tr4a4K', '', 1, '', NULL, '127.0.0.1', '', '2026-07-25 11:24:50', '', '', 'web', 2, '2026-07-23 02:21:16', '2026-07-25 11:24:50', NULL);
INSERT INTO `user_account` VALUES (934035802554576898, 'testuser', '', '13800138002', 'test@example.com', '$2y$10$RTRidRM2EtMIH6pAhsula..8xqM84yh9CPqN3/5pX5JpKP9vscA9e', 'salt', 1, '', NULL, '', '', NULL, '', '', 'web', 1, '2026-07-23 02:21:47', '2026-07-23 04:50:13', NULL);

-- ----------------------------
-- Table structure for wf_flow_apply
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_apply`;
CREATE TABLE `wf_flow_apply`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '申请单ID',
  `apply_no` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '审批单号',
  `flow_type_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '审批类型ID',
  `flow_def_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '流程模板ID',
  `title` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '单据标题',
  `apply_user_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '发起人UID',
  `dept_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '发起人部门ID',
  `form_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '表单提交内容JSON',
  `current_node_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '当前待审批节点ID',
  `current_approve_uid` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '当前待处理审批人',
  `apply_status` tinyint NOT NULL DEFAULT 0 COMMENT '单据总状态',
  `remark` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '发起人备注',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_apply_no`(`apply_no` ASC) USING BTREE,
  INDEX `idx_apply_user_id`(`apply_user_id` ASC) USING BTREE,
  INDEX `idx_flow_def_id`(`flow_def_id` ASC) USING BTREE,
  INDEX `idx_apply_status`(`apply_status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733862003212321 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wf_flow_apply
-- ----------------------------

-- ----------------------------
-- Table structure for wf_flow_approve_record
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_approve_record`;
CREATE TABLE `wf_flow_approve_record`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `apply_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联申请单ID',
  `node_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '流程节点ID',
  `approve_user_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作审批人UID',
  `action_type` tinyint NOT NULL DEFAULT 0 COMMENT '操作类型',
  `target_user_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '转审/加签目标人ID',
  `approve_opinion` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '审批意见',
  `attach_files` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '附件地址JSON数组',
  `operate_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_apply_id`(`apply_id` ASC) USING BTREE,
  INDEX `idx_approve_user_id`(`approve_user_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733862003213621 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wf_flow_approve_record
-- ----------------------------

-- ----------------------------
-- Table structure for wf_flow_cc_user
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_cc_user`;
CREATE TABLE `wf_flow_cc_user`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `apply_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请单ID',
  `cc_uid` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '被抄送用户ID',
  `is_read` tinyint NOT NULL DEFAULT 0 COMMENT '0未读 1已读',
  `read_time` datetime NULL DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_apply_cc_uid`(`apply_id` ASC, `cc_uid` ASC) USING BTREE,
  INDEX `idx_cc_uid`(`cc_uid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733862003203210 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wf_flow_cc_user
-- ----------------------------

-- ----------------------------
-- Table structure for wf_flow_definition
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_definition`;
CREATE TABLE `wf_flow_definition`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '流程定义ID',
  `flow_type_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联流程类型ID',
  `flow_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '流程名称',
  `version` int NOT NULL DEFAULT 1 COMMENT '版本号',
  `remark` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注说明',
  `apply_scope` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '可发起人员范围JSON',
  `is_publish` tinyint NOT NULL DEFAULT 0 COMMENT '是否发布 0草稿 1已发布',
  `created_by` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人用户ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_flow_type_id`(`flow_type_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733862004256572 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wf_flow_definition
-- ----------------------------

-- ----------------------------
-- Table structure for wf_flow_form
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_form`;
CREATE TABLE `wf_flow_form`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `flow_def_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '绑定流程定义ID',
  `field_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字段中文名称',
  `field_key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '字段英文标识',
  `field_type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '组件类型',
  `field_options` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '选项JSON',
  `is_required` tinyint NOT NULL DEFAULT 1 COMMENT '是否必填',
  `sort` int NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_flow_def_id`(`flow_def_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733862003211464 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wf_flow_form
-- ----------------------------

-- ----------------------------
-- Table structure for wf_flow_node
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_node`;
CREATE TABLE `wf_flow_node`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '节点ID',
  `flow_def_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联流程定义ID',
  `node_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '节点名称',
  `node_sort` int NOT NULL DEFAULT 1 COMMENT '节点执行顺序',
  `approve_type` tinyint NOT NULL DEFAULT 2 COMMENT '审批人员类型',
  `approve_target` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '审批目标值JSON',
  `node_mode` tinyint NOT NULL DEFAULT 1 COMMENT '节点审批模式',
  `can_reject` tinyint NOT NULL DEFAULT 1 COMMENT '是否可驳回',
  `can_add_sign` tinyint NOT NULL DEFAULT 1 COMMENT '是否允许加签',
  `can_transfer` tinyint NOT NULL DEFAULT 1 COMMENT '是否允许转审',
  `back_node_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '驳回回退节点ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_flow_def_id`(`flow_def_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733862004251215 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wf_flow_node
-- ----------------------------

-- ----------------------------
-- Table structure for wf_flow_node_condition
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_node_condition`;
CREATE TABLE `wf_flow_node_condition`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `flow_def_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属流程ID',
  `pre_node_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '上一个节点ID',
  `condition_field` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '条件字段',
  `condition_operator` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '运算符',
  `condition_value` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '阈值数值',
  `jump_node_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '满足条件跳转节点ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_flow_def_id`(`flow_def_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733862004251209 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wf_flow_node_condition
-- ----------------------------

-- ----------------------------
-- Table structure for wf_flow_type
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_type`;
CREATE TABLE `wf_flow_type`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `type_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '流程类型名称',
  `type_code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '唯一编码',
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '前端图标',
  `sort` int NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态 0禁用 1启用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_type_code`(`type_code` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 920733862004256492 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wf_flow_type
-- ----------------------------
INSERT INTO `wf_flow_type` VALUES (920733862004256487, '请假审批', 'leave', 'Calendar', 100, 1, '2026-07-25 11:18:13', '2026-07-25 11:18:13', NULL);
INSERT INTO `wf_flow_type` VALUES (920733862004256488, '费用报销', 'reimburse', 'Wallet', 90, 1, '2026-07-25 11:18:13', '2026-07-25 11:18:13', NULL);
INSERT INTO `wf_flow_type` VALUES (920733862004256489, '采购申请', 'purchase', 'ShoppingCart', 80, 1, '2026-07-25 11:18:13', '2026-07-25 11:18:13', NULL);
INSERT INTO `wf_flow_type` VALUES (920733862004256490, '商品上架审批', 'product_online', 'Goods', 70, 1, '2026-07-25 11:18:13', '2026-07-25 11:18:13', NULL);
INSERT INTO `wf_flow_type` VALUES (920733862004256491, '客户入驻审批', 'customer_audit', 'UserFilled', 60, 1, '2026-07-25 11:18:13', '2026-07-25 11:18:13', NULL);

SET FOREIGN_KEY_CHECKS = 1;
