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

