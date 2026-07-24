登录、注册、用户模块已按 `user_account` 表打通，前后台都可用。

### 默认账号
- 用户名：`admin`
- 密码：`admin123`

### 访问地址
| 端 | 地址 |
|---|---|
| 后台登录 | http://127.0.0.1:8000/backend/login |
| 后台注册 | http://127.0.0.1:8000/backend/register |
| 用户管理 | http://127.0.0.1:8000/backend/users |
| 前台首页 | http://127.0.0.1:8000/frontend/home |
| 前台登录/注册 | `/frontend/login` · `/frontend/register` |

### 后端能力
- `user_account` 迁移 / `UserAccount` 模型 / 状态枚举
- 登录（用户名/手机/邮箱）、注册、退出、`/auth/me`
- 用户 CRUD + 状态变更（禁用/冻结/注销）
- Guard：`backend` / `frontend` 分离会话

### 前端能力
- 后台：登录/注册页、路由守卫、用户管理列表
- 前台：首页 + 登录/注册页

启动：`php artisan serve` + `npm run dev`
————————————————————————————————————————————————————————————
角色管理模块已按 `auth_role` 表生成，接口与页面可用。

### 后端
- 迁移 / 模型：`auth_role` · `AuthRole`
- 枚举：`RoleType` / `DataScope` / `RoleStatus`
- 错误码：`AuthRoleError` + `CodePrefix::AUTH_ROLE = 810`
- 能力：分页 CRUD、排序、启停；系统角色不可删、不可改 `role_code`

| 接口 | 说明 |
|---|---|
| `GET /backend/api/roles` | 列表（关键词/类型/状态/数据权限） |
| `POST/PUT/PATCH/DELETE ...` | 增改删 / 排序 / 状态 |

### 前端
- 页面：`/backend/roles`
- 侧栏入口：权限菜单分类管理 → 角色管理
- 支持数据权限「自定义部门」时填写部门 ID

默认种子角色：`super_admin`（超级管理员）、`admin`（管理员）。
———————————————————————————————————————————————————————————————
权限模块已按 `auth_permissions` 表生成，接口与页面可用。

### 后端
- 迁移 / 模型：`auth_permissions` · `AuthPermission`
- 枚举：`PermissionType`（menu/button/api）· `PermissionStatus`
- 错误码：`AuthPermissionError` + `CodePrefix::AUTH_PERMISSION = 820`
- 能力：树形 CRUD、排序、启停；`api` 类型强制 HTTP 方法；有子节点不可删

| 接口 | 说明 |
|---|---|
| `GET /backend/api/permissions` | 管理树（可按关键词/类型筛选） |
| `GET /backend/api/permissions/tree` | 启用权限树（供角色授权） |
| `POST/PUT/PATCH/DELETE ...` | 增改删 / 排序 / 状态 |

### 前端
- 页面：`/backend/permissions`（树表 + 类型筛选）
- 侧栏：权限菜单分类管理 → 权限管理

库中已有 47 条权限数据；种子会补齐菜单入口并在空表时写入用户/角色/菜单/权限基础规则。