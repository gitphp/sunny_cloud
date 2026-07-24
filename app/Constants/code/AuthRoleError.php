<?php

namespace App\Constants\Code;

class AuthRoleError
{
    const NOT_FOUND = 1;              // 角色不存在
    const CODE_DUPLICATED = 2;        // 角色标识已存在
    const NAME_DUPLICATED = 3;        // 角色名称已存在
    const SYSTEM_FORBIDDEN = 4;       // 系统内置角色不可删除/修改标识
    const INVALID_DATA_SCOPE = 5;     // 数据权限范围无效
    const SCOPE_DEPTS_REQUIRED = 6;   // 自定义部门不能为空
    const INVALID_MENU_IDS = 7;       // 存在无效菜单
    const INVALID_PERMISSION_IDS = 8; // 存在无效权限
}
