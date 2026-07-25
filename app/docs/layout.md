后台前端组件架构已按图搭好，访问地址：`http://127.0.0.1:8000/backend/news/categories`

### 目录结构

```
resources/js/backend/          # 后台 Vue 3.5 SPA
├── main.js / App.vue
├── router/index.js
├── layouts/AdminLayout.vue
├── components/layout/         # Sidebar / Header / Tabs / Footer
├── views/news/Category.vue    # 分类管理（树表）
├── api/category.js
├── config/menus.js
└── styles/admin.css           # 青绿主题 #26c2bc

resources/js/frontend/         # 前台预留

app/Http/Controllers/backend/  # Category / Index
app/Service/CategoryService.php
app/Http/Requests|Resources/backend/
database/migrations/..._create_category_table.php
routes/backend.php
```

### 已实现（对齐截图）

- 深色侧栏 + 快捷色块 + 手风琴菜单（新闻 → 分类管理高亮）
- 顶栏工具图标、多标签页、页脚版权
- 分类树表：搜索 / 添加 / 修改 / 删除 / 排序号行内编辑
- Laravel API：`GET/POST/PUT/PATCH/DELETE /backend/api/categories`

### 启动

```bash
php artisan serve
npm run dev
```

然后打开：http://127.0.0.1:8000/backend


————————————————————————————————————————————————————————————————————————————

菜单模块已按 `auth_menus` 表生成，侧栏会从接口动态加载。

### 后端
- 迁移 / 模型：`auth_menus` · `AuthMenu` · `MenuStatus`
- 错误码：`AuthMenuError` + `CodePrefix::AUTH_MENU = 800`
- 服务 / 接口：树形 CRUD、排序、启停、侧栏导航

| 接口 | 说明 |
|---|---|
| `GET /backend/api/menus` | 管理树（含禁用） |
| `GET /backend/api/menus/nav` | 侧栏导航（仅启用） |
| `POST/PUT/PATCH/DELETE ...` | 增改删 / 排序 / 状态 |

### 前端
- 菜单管理页：`/backend/menus`（树表 + 搜索/添加/修改/删除/排序/启停）
- 侧栏改为请求 `/menus/nav`，失败时回退本地 `menus.js`

库里已有 27 条菜单数据，种子会跳过；前端已 build。打开后台即可在「权限菜单分类管理 → 菜单管理」使用。

————————————————————————————————————————————————————————————————————————————
已完成。继承结构如下：

```
Controller
 └── BaseController          # 统一 success() / error()
      ├── backend\AbstractController
      └── frontend\AbstractController
```

**改动要点：**
- `BaseController`：封装统一 API 响应
- 新增 `backend/AbstractController`、`frontend/AbstractController`
- 前后台所有控制器改为继承各自的 `AbstractController`，并用 `$this->success()` / `$this->error()` 替代直接调用 `ApiResponseHelper`
- 删除未使用的 `v1/AbstractController`

后续前后台公共逻辑可分别加在对应 `AbstractController` 中。
——————————————————————————————————————————————————————————————————————————————
已按新 `category` 表结构对齐前后端。

**字段映射**
| 旧 | 新 |
|---|---|
| `name` | `category_name` |
| `sort` | `sort_order` |
| — | `show_type` / `cat_status` / `level` / `description` / `cat_remark` / `deleted_by` |

**主要更新**
- Migration、Model、Seeder 对齐新表
- 枚举：`CategoryShowType` / `CategoryStatus` / `CategoryLevel`
- Service：自动算 `level`（最多三级）、同名校验、有子分类不可删、状态切换
- 接口新增：`PATCH /categories/{id}/status`
- 前端树表：名称/级别/可见性/排序/显隐开关，表单含描述与备注

刷新后台分类页即可验证。若库是空的，可跑 `php artisan db:seed --class=CategorySeeder`。