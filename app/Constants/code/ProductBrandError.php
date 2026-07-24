<?php

namespace App\Constants\Code;



class ProductBrandError 
{
    const NOT_FOUND = 1;    // 品牌不存在
    const NAME_DUPLICATED = 2;    // 品牌名称已存在
    const DELETE_BLOCKED_SYSTEM = 3;    // 系统预设品牌不可删除
    const INVALID_BATCH_REQUEST = 4;    // 批量删除参数无效
}
