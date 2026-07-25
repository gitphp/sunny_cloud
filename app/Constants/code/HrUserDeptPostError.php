<?php

namespace App\Constants\Code;

class HrUserDeptPostError
{
    const NOT_FOUND = 1;                 // 任职记录不存在
    const USER_NOT_FOUND = 2;            // 用户不存在
    const DEPT_NOT_FOUND = 3;            // 部门不存在
    const POST_NOT_FOUND = 4;            // 岗位不存在
    const DUPLICATED = 5;                // 同一员工在同一部门不能重复挂同一岗位
    const MAIN_ALREADY_EXISTS = 6;       // 该员工已有主岗
    const DATE_RANGE_INVALID = 7;        // 任职结束时间不能早于开始时间
}
