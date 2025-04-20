<?php

namespace app\front_api\controller;

use app\front_api\logic\SmsLogic;
use app\front_api\validate\SendSmsValidate;

/**
 * 短信
 * @class SmsController
 * @package app\front_api\controller
 * @author LZH
 * @date 2025/2/19
 */
class SmsController extends BaseApiController
{

    public array $notNeedLogin = ['sendCode'];

    /**
     * 发送短信验证码
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function sendCode()
    {
        $params = (new SendSmsValidate())->post()->goCheck();
        $result = SmsLogic::sendCode($params);
        if (true === $result) {
            return $this->success('发送成功');
        }
        return $this->fail(SmsLogic::getError());
    }

}