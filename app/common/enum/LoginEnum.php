<?php
declare(strict_types=1);

namespace app\common\enum;


/**
 * 登录枚举
 * @class LoginEnum
 * @package app\common\enum
 * @author LZH
 * @date 2025/2/18
 */
class LoginEnum
{
    /**
     * 支持的登录方式
     * ACCOUNT_PASSWORD 账号/手机号密码登录
     * MOBILE_CAPTCHA 手机验证码登录
     * THIRD_LOGIN 第三方登录
     */
    const ACCOUNT_PASSWORD = 1;
    const MOBILE_CAPTCHA = 2;
    const THIRD_LOGIN = 3;
}