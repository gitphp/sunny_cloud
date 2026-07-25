产品基础资料模块已按表结构生成。

### 表
- `product_brand` 品牌（编码 BR000001）
- `product_category` 商品分类树（编码 FL000001，最多三级）
- `product_specification` 规格（编码 GL000001）
- `product_specification_value` 规格值（编码 GV000001，含 `spec_id`）

### 页面
| 地址 | 说明 |
|---|---|
| `/backend/product/brands` | 品牌管理 |
| `/backend/product/categories` | 商品分类 |
| `/backend/product/specifications` | 规格 + 规格值弹窗 |

### 接口前缀
`/backend/api/product/brands|categories|specifications|specification-values`

### 种子
```bash
php artisan migrate
php artisan db:seed --class=ProductSeeder
npm run build
```
