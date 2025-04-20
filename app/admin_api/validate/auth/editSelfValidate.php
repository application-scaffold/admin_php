<?php
declare(strict_types=1);

namespace app\admin_api\validate\auth;

use app\common\validate\BaseValidate;
use app\common\model\auth\Admin;
use think\facade\Config;

/**
 * 编辑超级管理员验证
 * @class editSelfValidate
 * @package app\admin_api\validate\auth
 * @author LZH
 * @date 2025/2/19
 */
class editSelfValidate extends BaseValidate
{

    protected $rule = [
        'name' => 'require|length:1,16',
        'avatar' => 'require',
        'password' => 'length:6,32|checkPassword',
        'password_confirm' => 'requireWith:password|confirm',
    ];


    protected $message = [
        'name.require' => '请填写名称',
        'name.length' => '名称须在1-16位字符',
        'avatar.require' => '请选择头像',
        'password_now.length' => '密码长度须在6-32位字符',
        'password_confirm.requireWith' => '确认密码不能为空',
        'password_confirm.confirm' => '两次输入的密码不一致',
    ];


    /**
     * 校验密码
     * @param bool $value
     * @param string $rule
     * @param array $data
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    public function checkPassword(bool $value, string $rule, array $data): bool|string
    {
        if (empty($data['password_old'])) {
            return '请填写当前密码';
        }

        $admin = Admin::findOrEmpty($data['admin_id']);
        $passwordSalt = Config::get('project.unique_identification');
        $oldPassword = create_password($data['password_old'], $passwordSalt);

        if ($admin['password'] != $oldPassword) {
            return '当前密码错误';
        }

        return true;
    }

}