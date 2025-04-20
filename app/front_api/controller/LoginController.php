<?php

namespace app\front_api\controller;

use app\front_api\validate\{LoginAccountValidate, RegisterValidate, WebScanLoginValidate, WechatLoginValidate};
use app\front_api\logic\LoginLogic;

/**
 * 登录注册
 * @class LoginController
 * @package app\front_api\controller
 * @author LZH
 * @date 2025/2/19
 */
class LoginController extends BaseApiController
{

    public array $notNeedLogin = ['register', 'account', 'logout', 'codeUrl', 'oaLogin',  'mnpLogin', 'getScanCode', 'scanLogin'];


    /**
     * 注册账号
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function register()
    {
        $params = (new RegisterValidate())->post()->goCheck('register');
        $result = LoginLogic::register($params);
        if (true === $result) {
            return $this->success('注册成功', [], 1, 1);
        }
        return $this->fail(LoginLogic::getError());
    }


    /**
     * 账号密码/手机号密码/手机号验证码登录
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function account()
    {
        $params = (new LoginAccountValidate())->post()->goCheck();
        $result = LoginLogic::login($params);
        if (false === $result) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->data($result);
    }

    /**
     * 退出登录
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function logout()
    {
        LoginLogic::logout($this->userInfo);
        return $this->success();
    }


    /**
     * 获取微信请求code的链接
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function codeUrl()
    {
        $url = $this->request->get('url');
        $result = ['url' => LoginLogic::codeUrl($url)];
        return $this->success('获取成功', $result);
    }

    /**
     * 公众号登录
     * @return \think\response\Json
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author LZH
     * @date 2025/2/19
     */
    public function oaLogin()
    {
        $params = (new WechatLoginValidate())->post()->goCheck('oa');
        $res = LoginLogic::oaLogin($params);
        if (false === $res) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('', $res);
    }

    /**
     * 小程序-登录接口
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function mnpLogin()
    {
        $params = (new WechatLoginValidate())->post()->goCheck('mnpLogin');
        $res = LoginLogic::mnpLogin($params);
        if (false === $res) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('', $res);
    }

    /**
     * 小程序绑定微信
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function mnpAuthBind()
    {
        $params = (new WechatLoginValidate())->post()->goCheck("wechatAuth");
        $params['user_id'] = $this->userId;
        $result = LoginLogic::mnpAuthLogin($params);
        if ($result === false) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('绑定成功', [], 1, 1);
    }


    /**
     * 公众号绑定微信
     * @return \think\response\Json
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author LZH
     * @date 2025/2/19
     */
    public function oaAuthBind()
    {
        $params = (new WechatLoginValidate())->post()->goCheck("wechatAuth");
        $params['user_id'] = $this->userId;
        $result = LoginLogic::oaAuthLogin($params);
        if ($result === false) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('绑定成功', [], 1, 1);
    }

    /**
     * 获取扫码地址
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function getScanCode()
    {
        $redirectUri = $this->request->get('url/s');
        $result = LoginLogic::getScanCode($redirectUri);
        if (false === $result) {
            return $this->fail(LoginLogic::getError() ?? '未知错误');
        }
        return $this->success('', $result);
    }

    /**
     * 网站扫码登录
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function scanLogin()
    {
        $params = (new WebScanLoginValidate())->post()->goCheck();
        $result = LoginLogic::scanLogin($params);
        if (false === $result) {
            return $this->fail(LoginLogic::getError() ?? '登录失败');
        }
        return $this->success('', $result);
    }

    /**
     * 更新用户头像昵称
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function updateUser()
    {
        $params = (new WechatLoginValidate())->post()->goCheck("updateUser");
        LoginLogic::updateUser($params, $this->userId);
        return $this->success('操作成功', [], 1, 1);
    }
}