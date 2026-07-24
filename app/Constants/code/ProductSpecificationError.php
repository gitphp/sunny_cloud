<?php
namespace App\Constants\Code;

class ProductSpecificationError
{
    const NOT_FOUND = 1;    // 规格不存在
    const NAME_DUPLICATED = 2;    // 规格名称已存在
    const VALUES_REQUIRED = 3;    // 请至少添加一个规格值
    const VALUE_NOT_FOUND = 4;    // 规格值不存在
    const VALUE_DUPLICATED = 5;    // 规格值已存在
    const REFERENCED_BY_PRODUCT = 6;    // 规格已被商品引用，不可删除
}
