<?php
namespace App\Constants\Code;

class ProductCategoryError  
{
    const NOT_FOUND = 1;    // 分类不存在
    const NAME_DUPLICATED = 2;    // 同级分类名称已存在
    const PARENT_NOT_FOUND = 3;    // 上级分类不存在
    const LEVEL_EXCEEDED = 4;    // 最多支持三级分类

    const DELETE_BLOCKED_HAS_CHILDREN = 5;    // 存在子分类，不可删除
    const DELETE_BLOCKED_HAS_PRODUCTS = 6;    // 分类下存在商品，不可删除
    const PARENT_INVALID = 7;    // 父分类无效
    const INVALID_BATCH_REQUEST = 8;    // 批量删除参数无效 （批量删除时，父分类不能为空）

    const VISIBILITY_CUSTOMER_REQUIRED = 9;    // 请选择客户

    const VISIBILITY_CUSTOMER_INVALID = 10;    // 存在无效的客户    

    const TRANSFER_PRODUCT_EMPTY = 11;    // 请选择要转移的商品

    const TRANSFER_TARGET_SAME = 12;

    const TRANSFER_PRODUCT_INVALID = 13;    // 所选商品不存在或不属于当前分类

    const TRANSFER_TARGET_NOT_FOUND = 14;    // 转移目标分类不存在
}
