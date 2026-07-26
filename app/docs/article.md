文章分类、文章模块已按 `article_category` / `articles` 表打通。

### 页面
| 页面 | 地址 |
|---|---|
| 分类管理 | `/backend/news/categories` |
| 文章列表 | `/backend/news/articles` |
| 写文章 | `/backend/news/articles/create` |
| 编辑文章 | `/backend/news/articles/{id}/edit` |

### 接口前缀
- `GET/POST/PUT/PATCH/DELETE /backend/api/news/article-categories`
- `GET/POST/PUT/PATCH/DELETE /backend/api/news/articles`

### 文章状态
1 草稿 · 2 待审核 · 3 审核通过 · 4 已发布 · 5 已下线 · 6 审核驳回 · 7 回收站

### 种子
`php artisan db:seed --class=ArticleSeeder`
