<?php
declare(strict_types=1);

namespace app\front_api\validate;

use app\common\validate\BaseValidate;


/**
 * 密码校验
 * @class PasswordValidate
 * @package app\front_api\validate
 * @author LZH
 * @date 2025/2/20
 */
class PasswordValidate extends BaseValidate
{

    protected $rule = [
        'mobile' => 'require|mobile',
        'code' => 'require',
        'password' => 'require|length:6,20|alphaNum',
        'password_confirm' => 'require|confirm',
    ];


    protected $message = [
        'mobile.require' => '请输入手机号',
        'mobile.mobile' => '请输入正确手机号',
        'code.require' => '请填写验证码',
        'password.require' => '请输入密码',
        'password.length' => '密码须在6-25位之间',
        'password.alphaNum' => '密码须为字母数字组合',
        'password_confirm.require' => '请确认密码',
        'password_confirm.confirm' => '两次输入的密码不一致'
    ];


    /**
     * 重置登录密码
     * @return PasswordValidate
     * @author LZH
     * @date 2025/2/20
     */
    public function sceneResetPassword(): PasswordValidate
    {
        return $this->only(['mobile', 'code', 'password', 'password_confirm']);
    }


    /**
     * 修改密码场景
     * @return PasswordValidate
     * @author LZH
     * @date 2025/2/20
     */
    public function sceneChangePassword(): PasswordValidate
    {
        return $this->only(['password', 'password_confirm']);
    }

}