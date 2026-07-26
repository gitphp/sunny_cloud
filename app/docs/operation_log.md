操作日志模块已按 `operation_log` 表打通。

### 能力
- 登录成功/失败写入 `LOGIN` 日志
- 用户、品牌 CRUD 写入 `INSERT/UPDATE/DELETE` 日志（含 old/new 快照）
- 后台列表筛选：关键词 / 模块 / 操作类型 / 状态 / 日期
- 详情抽屉查看 JSON 快照、IP、UA、URL

### 接口
| 接口 | 说明 |
|---|---|
| `GET /backend/api/operation-logs` | 分页列表 |
| `GET /backend/api/operation-logs/{id}` | 详情 |

### 页面
`/backend/system/operation-logs`

### 写日志
```php
app(OperationLogService::class)->logCrud(
    OperationAction::Update,
    OperationBizType::ProductBrand,
    'product_brand_updated',
    $id,
    $label,
    $old,
    $new,
    'XxxService@update'
);
```
