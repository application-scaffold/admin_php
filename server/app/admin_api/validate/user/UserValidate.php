<?php

namespace app\admin_api\validate\user;

use app\common\model\user\User;
use app\common\validate\BaseValidate;

/**
 * 用户验证
 * @class UserValidate
 * @package app\admin_api\validate\user
 * @author LZH
 * @date 2025/2/19
 */
class UserValidate extends BaseValidate
{

    protected $rule = [
        'id' => 'require|checkUser',
        'field' => 'require|checkField',
        'value' => 'require',
    ];

    protected $message = [
        'id.require' => '请选择用户',
        'field.require' => '请选择操作',
        'value.require' => '请输入内容',
    ];

    /**
     * 详情场景
     * @return UserValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    /**
     * 用户信息校验
     * @param $value
     * @param $rule
     * @param $data
     * @return string|true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function checkUser($value, $rule, $data)
    {
        $userIds = is_array($value) ? $value : [$value];

        foreach ($userIds as $item) {
            if (!User::find($item)) {
                return '用户不存在！';
            }
        }
        return true;
    }


    /**
     * 校验是否可更新信息
     * @param $value
     * @param $rule
     * @param $data
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    public function checkField($value, $rule, $data)
    {
        $allowField = ['account', 'sex', 'mobile', 'real_name'];

        if (!in_array($value, $allowField)) {
            return '用户信息不允许更新';
        }

        switch ($value) {
            case 'account':
                //验证手机号码是否存在
                $account = User::where([
                    ['id', '<>', $data['id']],
                    ['account', '=', $data['value']]
                ])->findOrEmpty();

                if (!$account->isEmpty()) {
                    return '账号已被使用';
                }
                break;

            case 'mobile':
                if (false == $this->validate($data['value'], 'mobile', $data)) {
                    return '手机号码格式错误';
                }

                //验证手机号码是否存在
                $mobile = User::where([
                    ['id', '<>', $data['id']],
                    ['mobile', '=', $data['value']]
                ])->findOrEmpty();

                if (!$mobile->isEmpty()) {
                    return '手机号码已存在';
                }
                break;
        }
        return true;
    }

}