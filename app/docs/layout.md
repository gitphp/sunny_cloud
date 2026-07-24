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