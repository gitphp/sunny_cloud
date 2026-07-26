运营相关模块已打通：用户留言、招聘职位、友情链接；网站设置在系统管理下。

### 页面
| 模块 | 地址 |
|---|---|
| 友情链接 | `/backend/friend-links` |
| 用户留言 | `/backend/feedbacks` |
| 招聘职位 | `/backend/boss-jobs` |
| 网站设置 | `/backend/system/settings` |

### 留言接口
| 接口 | 说明 |
|---|---|
| `GET /backend/api/feedbacks` | 后台列表 |
| `GET /backend/api/feedbacks/{id}` | 详情 |
| `POST /backend/api/feedbacks/{id}/reply` | 回复（自动置为已处理） |
| `DELETE /backend/api/feedbacks/{id}` | 删除 |
| `POST /frontend/api/feedbacks` | 前台提交留言（限流） |

### 职位接口
| 接口 | 说明 |
|---|---|
| `GET/POST/PUT/DELETE /backend/api/boss-jobs` | 列表/增改删 |
| `PATCH .../sort` · `.../status` · `.../hot` | 排序/状态/急聘 |

### 状态
- 留言：0 未处理 · 1 已处理
- 职位：1 待发布 · 2 发布中 · 3 已关闭

### 种子
`php artisan db:seed --class=OperationModuleSeeder`
