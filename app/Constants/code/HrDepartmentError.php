<?php

namespace App\Constants\Code;

class HrDepartmentError
{
    const NOT_FOUND = 1;                     // 部门不存在
    const NAME_DUPLICATED = 2;               // 同级部门名称已存在
    const CODE_DUPLICATED = 3;               // 部门编码已存在
    const PARENT_NOT_FOUND = 4;              // 上级部门不存在
    const PARENT_INVALID = 5;                // 上级部门无效
    const DELETE_BLOCKED_HAS_CHILDREN = 6;   // 存在子部门，不可删除
    const DELETE_BLOCKED_HAS_USERS = 7;      // 部门下存在任职人员，不可删除
    const LEADER_USER_INVALID = 8;           // 负责人用户无效
    const LEADER_DUPLICATED = 9;             // 同一用户不能重复担任负责人
}
