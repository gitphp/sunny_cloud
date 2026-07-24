<?php

namespace App\Constants\Code;

class AuthPermissionError
{
    const NOT_FOUND = 1;                 // 权限不存在
    const CODE_DUPLICATED = 2;           // 权限标识已存在
    const NAME_DUPLICATED = 3;           // 同级权限名称已存在
    const PARENT_NOT_FOUND = 4;          // 上级权限不存在
    const PARENT_INVALID = 5;            // 上级权限无效
    const DELETE_BLOCKED_HAS_CHILDREN = 6; // 存在子权限，不可删除
    const METHOD_REQUIRED = 7;           // 接口类型必须填写 HTTP 方法
}
