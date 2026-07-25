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

————————————————————————
