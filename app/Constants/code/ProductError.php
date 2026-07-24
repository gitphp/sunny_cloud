<?php
namespace App\Constants\Code;

class ProductError
{
    const NOT_FOUND = 1;    // 商品不存在
    const CATEGORY_NOT_FOUND = 2;    // 商品分类不存在
    const BRAND_NOT_FOUND = 3;    // 商品品牌不存在
    const MEDIA_NOT_FOUND = 4;    // 附件不存在
    const SKU_NOT_FOUND = 5;    // SKU不存在
    const SKU_SPEC_VALUE_NOT_FOUND = 6;    // 规格值不存在或不可用
    const SKU_SPEC_DIMENSION_DUPLICATED = 7;    // 同一SKU不能包含同一规格的多个值
    const SKU_COMBO_DUPLICATED = 8;    // SKU规格组合重复
    const MATERIAL_NOT_FOUND = 9;    // 关联物料不存在
}
