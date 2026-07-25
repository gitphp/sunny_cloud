商品主表 / SKU / 图文资质模块已生成。

### 表
- `product` 商品主表（编码 SP000001）
- `product_sku` SKU 价格（编码 SKU000001）
- `product_sku_spec_value` SKU-规格值关联
- `product_media` 图文资质附件

### 本地上传
文件保存在 `public/uploads/products/Y/m/`，URL 形如 `/uploads/products/2026/07/xxx.jpg`

### 页面
| 地址 | 说明 |
|---|---|
| `/backend/product/products` | 商品列表 |
| `/backend/product/products/create` | 新增（三 Tab） |
| `/backend/product/products/:id/edit` | 编辑 |

三 Tab：基础信息 / 规格定价（笛卡尔积生成 SKU） / 图文资质

```bash
php artisan migrate
php artisan db:seed --class=ProductSeeder
npm run build
```
——————————————————————————————————————————————————————————————————————————————————

审批流程配置模块已按 `wf_flow_type` / `wf_flow_definition` / `wf_flow_form` / `wf_flow_node` 生成。

### 后端
- 迁移 / 模型 / 枚举 / 错误码（860 流程类型 · 870 流程模板）
- 流程类型 CRUD + 排序 / 启停 + options
- 流程模板 CRUD（嵌套表单字段 + 审批节点）+ 发布 / 设草稿

| 接口 | 说明 |
|---|---|
| `GET/POST/PUT/PATCH/DELETE /backend/api/wf/flow-types` | 类型增改删 / 排序 / 状态 |
| `GET /backend/api/wf/flow-types/options` | 启用类型下拉 |
| `GET/POST/PUT/DELETE /backend/api/wf/flow-definitions` | 模板列表 / 详情 / 保存 / 删除 |
| `POST .../flow-definitions/{id}/publish\|unpublish` | 发布 / 设草稿 |

### 前端
- 页面：`/backend/wf/flow-types` · `/backend/wf/flow-definitions`（含 create / edit）
- 侧栏：流程管理 → 流程类型 / 流程模板
- 模板配置 Tab：基本信息 / 表单字段 / 审批节点

### 种子
`WfSeeder`：预设 leave / reimburse / purchase / product_online / customer_audit + 菜单，并授权给 `super_admin` / `admin`。

```bash
php artisan migrate
php artisan db:seed --class=WfSeeder
```

