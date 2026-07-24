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