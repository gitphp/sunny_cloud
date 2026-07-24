<?php

namespace App\Constants\Code;

class UserError
{
    const NOT_FOUND = 1;           // 用户不存在
    const INVALID_ROLE_IDS = 2;    // 存在无效角色
    const ACCOUNT_OR_PASSWORD_ERROR = 3; // 账号或密码错误
    const ACCOUNT_DISABLED = 4;    // 账号不可登录
}
