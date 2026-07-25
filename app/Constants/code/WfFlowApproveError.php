<?php

namespace App\Constants\Code;

class WfFlowApproveError
{
    const NOT_FOUND = 1;
    const STATUS_INVALID = 2;
    const NO_PERMISSION = 3;
    const NODE_NOT_ALLOW_REJECT = 4;
    const NODE_NOT_ALLOW_TRANSFER = 5;
    const NODE_NOT_ALLOW_ADD_SIGN = 6;
    const TARGET_REQUIRED = 7;
    const ALREADY_ACTED = 8;
    const NO_APPROVER = 9;
}
