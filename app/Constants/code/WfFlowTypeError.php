<?php

namespace App\Constants\Code;

class WfFlowTypeError
{
    const NOT_FOUND = 1;           // 流程类型不存在
    const NAME_DUPLICATED = 2;     // 流程类型名称已存在
    const CODE_DUPLICATED = 3;     // 流程类型编码已存在
    const DELETE_BLOCKED_HAS_FLOW = 4; // 存在流程定义，不可删除
}
