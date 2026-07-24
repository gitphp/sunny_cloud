<?php
namespace App\Constants\Code;

class Common
{
    // 数据不存在
    const DATA_NOT_FOUND_ERROR = 1;

    // 数据验证错误
    const DATA_VALIDATION_ERROR = 2;

    // 数据重复
    const DUPLICATED_ERROR = 3;

    // 日期无效
    const INVALID_DATE = 4;

    // 无效请求
    const INVALID_REQUEST = 5;

    // 策略错误
    const STRATEGY_ERROR = 6;

    // 系统预设禁止
    const SYSTEM_PRESET_FORBIDDEN = 7;

    // 仅草稿可编辑
    const VOUCHER_DRAFT_ONLY_EDIT = 8;

    // 仅草稿可删除
    const VOUCHER_DRAFT_ONLY_DELETE = 9;

    // 仅草稿可提交
    const VOUCHER_DRAFT_ONLY_SUBMIT = 10;

    // 仅已提交可撤销
    const VOUCHER_SUBMITTED_ONLY_REVERT = 11;

    // 仅已提交可审核
    const VOUCHER_SUBMITTED_ONLY_APPROVE = 12;

    // 仅已审核可取消审核
    const VOUCHER_APPROVED_ONLY_UNAPPROVE = 13;

    // 仅已提交可驳回
    const VOUCHER_SUBMITTED_ONLY_REJECT = 14;

    // 状态错误
    const STATUS_ERROR = 15;

    // 状态不能被修改
    const STATUS_CANNOT_BE_MODIFIED = 16;

    // 分类被用了，禁止删除
    const CATEGORY_USED_ERROR = 17;

    // 审批中心服务错误
    const APPROVAL_CENTER_SERVICE_ERROR = 18;

    // 租户ID无效
    const INVALID_TENANT_ID = 19;

    // 编码不能为空
    const CODE_REQUIRED = 20;

    // 未知错误
    const UNKNOWN_ERROR = 98;

    // 网络错误
    const NETWORK_ERROR = 99;
}
