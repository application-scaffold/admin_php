<?php

namespace app\front_api\controller;

use app\front_api\logic\UserLogic;
use app\front_api\validate\PasswordValidate;
use app\front_api\validate\SetUserInfoValidate;
use app\front_api\validate\UserValidate;

/**
 * 用户控制器
 * @class UserController
 * @package app\front_api\controller
 * @author LZH
 * @date 2025/2/19
 */
class UserController extends BaseApiController
{
    public array $notNeedLogin = ['resetPassword'];

    /**
     * 获取个人中心
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function center()
    {
        $data = UserLogic::center($this->userInfo);
        return $this->success('', $data);
    }

    /**
     * 获取个人信息
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function info()
    {
        $result = UserLogic::info($this->userId);
        return $this->data($result);
    }

    /**
     * 重置密码
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function resetPassword()
    {
        $params = (new PasswordValidate())->post()->goCheck('resetPassword');
        $result = UserLogic::resetPassword($params);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(UserLogic::getError());
    }

    /**
     * 修改密码
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function changePassword()
    {
        $params = (new PasswordValidate())->post()->goCheck('changePassword');
        $result = UserLogic::changePassword($params, $this->userId);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(UserLogic::getError());
    }

    /**
     * 获取小程序手机号
     * @return \think\response\Json
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @author LZH
     * @date 2025/2/19
     */
    public function getMobileByMnp()
    {
        $params = (new UserValidate())->post()->goCheck('getMobileByMnp');
        $params['user_id'] = $this->userId;
        $result = UserLogic::getMobileByMnp($params);
        if ($result === false) {
            return $this->fail(UserLogic::getError());
        }
        return $this->success('绑定成功', [], 1, 1);
    }

    /**
     * 编辑用户信息
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function setInfo()
    {
        $params = (new SetUserInfoValidate())->post()->goCheck(null, ['id' => $this->userId]);
        $result = UserLogic::setInfo($this->userId, $params);
        if (false === $result) {
            return $this->fail(UserLogic::getError());
        }
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * 绑定/变更 手机号
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function bindMobile()
    {
        $params = (new UserValidate())->post()->goCheck('bindMobile');
        $params['user_id'] = $this->userId;
        $result = UserLogic::bindMobile($params);
        if($result) {
            return $this->success('绑定成功', [], 1, 1);
        }
        return $this->fail(UserLogic::getError());
    }

}