<?php

namespace App\Constants\Code;

class HrPostError
{
    const NOT_FOUND = 1;                     // 岗位不存在
    const NAME_DUPLICATED = 2;               // 同级岗位名称已存在
    const CODE_DUPLICATED = 3;               // 岗位编码已存在
    const PARENT_NOT_FOUND = 4;              // 上级岗位不存在
    const PARENT_INVALID = 5;                // 上级岗位无效
    const DELETE_BLOCKED_HAS_CHILDREN = 6;   // 存在子岗位，不可删除
    const DELETE_BLOCKED_HAS_USERS = 7;      // 岗位下存在任职人员，不可删除
}
