<?php
declare(strict_types=1);

namespace app\admin_api\validate\auth;

use app\common\validate\BaseValidate;
use app\common\model\auth\Admin;

/**
 * 管理员验证
 * @class AdminValidate
 * @package app\admin_api\validate\auth
 * @author LZH
 * @date 2025/2/19
 */
class AdminValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkAdmin',
        'account' => 'require|length:1,32|unique:'.Admin::class,
        'name' => 'require|length:1,16|unique:'.Admin::class,
        'password' => 'require|length:6,32|edit',
        'password_confirm' => 'requireWith:password|confirm',
        'role_id' => 'require',
        'disable' => 'require|in:0,1|checkAbleDisable',
        'multipoint_login' => 'require|in:0,1',
    ];

    protected $message = [
        'id.require' => '管理员id不能为空',
        'account.require' => '账号不能为空',
        'account.length' => '账号长度须在1-32位字符',
        'account.unique' => '账号已存在',
        'password.require' => '密码不能为空',
        'password.length' => '密码长度须在6-32位字符',
        'password_confirm.requireWith' => '确认密码不能为空',
        'password_confirm.confirm' => '两次输入的密码不一致',
        'name.require' => '名称不能为空',
        'name.length' => '名称须在1-16位字符',
        'name.unique' => '名称已存在',
        'role_id.require' => '请选择角色',
        'disable.require' => '请选择状态',
        'disable.in' => '状态值错误',
        'multipoint_login.require' => '请选择是否支持多处登录',
        'multipoint_login.in' => '多处登录状态值为误',
    ];

    /**
     * 添加场景
     * @return AdminValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneAdd(): AdminValidate
    {
        return $this->remove(['password', 'edit'])
            ->remove('id', true)
            ->remove('disable', true);
    }

    /**
     * 详情场景
     * @return AdminValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDetail(): AdminValidate
    {
        return $this->only(['id']);
    }

    /**
     * 编辑场景
     * @return AdminValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneEdit(): AdminValidate
    {
        return $this->remove('password', 'require|length')
            ->append('id', 'require|checkAdmin')
            ->remove('role_id', 'require')
            ->append('role_id', 'checkRole');
    }

    /**
     * 删除场景
     * @return AdminValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDelete(): AdminValidate
    {
        return $this->only(['id']);
    }

    /**
     * 编辑情况下，检查是否填密码
     * @param string $value
     * @param string $rule
     * @param array $data
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    public function edit(string $value, string $rule, array $data): bool|string
    {
        if (empty($data['password']) && empty($data['password_confirm'])) {
            return true;
        }
        $len = strlen($value);
        if ($len < 6 || $len > 32) {
            return '密码长度须在6-32位字符';
        }
        return true;
    }

    /**
     * 检查指定管理员是否存在
     * @param string $value
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    public function checkAdmin(string $value): bool|string
    {
        $admin = Admin::findOrEmpty($value);
        if ($admin->isEmpty()) {
            return '管理员不存在';
        }
        return true;
    }


    /**
     * 禁用校验
     * @param bool $value
     * @param string $rule
     * @param array $data
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    public function checkAbleDisable(bool $value, string $rule, array $data): bool|string
    {
        $admin = Admin::findOrEmpty($data['id']);
        if ($admin->isEmpty()) {
            return '管理员不存在';
        }

        if ($value && $admin['root']) {
            return '超级管理员不允许被禁用';
        }
        return true;
    }

    /**
     * 校验角色
     * @param bool $value
     * @param string $rule
     * @param array $data
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    public function checkRole(bool $value, string $rule, array $data)
    {
        $admin = Admin::findOrEmpty($data['id']);
        if ($admin->isEmpty()) {
            return '管理员不存在';
        }

        if ($admin['root']) {
            return true;
        }

        if (empty($data['role_id'])) {
            return '请选择角色';
        }

        return true;
    }

}