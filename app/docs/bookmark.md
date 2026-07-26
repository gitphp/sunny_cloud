书签模块已按 `book_mark` 表打通，分类关联通用 `category` 表。

### 页面
`/backend/bookmarks`

### 接口
| 接口 | 说明 |
|---|---|
| `GET /backend/api/bookmarks` | 分页列表（关键词/分类/状态） |
| `POST/PUT /backend/api/bookmarks` | 新增 / 修改 |
| `PATCH .../sort` · `.../status` | 排序 / 状态 |
| `DELETE ...` | 删除 |

### 状态
0 隐藏 · 1 正常 · 2 失效

### 字体 is_bold
0 加粗 · 1 正常（与表注释一致）

### 种子
`php artisan db:seed --class=BookMarkSeeder`
