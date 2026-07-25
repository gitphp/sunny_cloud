<?php

namespace App\Constants\Code;

class WfFlowDefinitionError
{
    const NOT_FOUND = 1;              // 流程定义不存在
    const TYPE_NOT_FOUND = 2;         // 流程类型不存在
    const NAME_DUPLICATED = 3;        // 同类型下流程名称已存在
    const FIELD_KEY_DUPLICATED = 4;   // 表单字段标识重复
    const NODE_EMPTY = 5;             // 请至少配置一个审批节点
    const NODE_TARGET_REQUIRED = 6;   // 请配置审批目标
    const ALREADY_PUBLISHED = 7;      // 流程已发布
    const PUBLISH_NEED_NODE = 8;      // 发布前请配置审批节点
    const BACK_NODE_INVALID = 9;      // 驳回回退节点无效
    const CONDITION_NODE_INVALID = 10; // 条件跳转节点无效
}
