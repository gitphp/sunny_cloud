<?php

namespace App\Constants\Code;

class AuthMenuError
{
    const NOT_FOUND = 1;                 // 菜单不存在
    const NAME_DUPLICATED = 2;           // 同级菜单名称已存在
    const PARENT_NOT_FOUND = 3;          // 上级菜单不存在
    const PARENT_INVALID = 4;            // 上级菜单无效（不能是自身或子级）
    const DELETE_BLOCKED_HAS_CHILDREN = 5; // 存在子菜单，不可删除
    const PATH_DUPLICATED = 6;           // 路由路径已存在
    const PERMISSION_CODE_DUPLICATED = 7; // 权限标识已存在
}
