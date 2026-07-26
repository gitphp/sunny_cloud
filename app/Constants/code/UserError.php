<?php

namespace App\Constants\Code;

class UserError
{
    /** 用户不存在 */
    public const NOT_FOUND = 1;

    /** 存在无效角色 */
    public const INVALID_ROLE_IDS = 2;

    /** 账号或密码错误（用户模块业务码，配合 CodePrefix::USER） */
    public const ACCOUNT_OR_PASSWORD_ERROR = 3;

    /** 账号不可登录（用户模块业务码，配合 CodePrefix::USER） */
    public const ACCOUNT_DISABLED = 4;

    /**
     * 认证接口固定错误码（前后端已约定，勿随意改动）
     */
    public const AUTH_ACCOUNT_OR_PASSWORD = 2001001;

    public const AUTH_ACCOUNT_DISABLED = 2001002;

    public const AUTH_NOT_LOGGED_IN = 2001003;

    public const AUTH_LOGIN_TOO_MANY = 2001004;

    public const AUTH_LOGIN_FAILED = 2001098;

    public const AUTH_REGISTER_FAILED = 2001099;

    public const AUTH_USERNAME_EXISTS = 2001010;

    public const AUTH_MOBILE_EXISTS = 2001011;

    public const AUTH_EMAIL_EXISTS = 2001012;
}
