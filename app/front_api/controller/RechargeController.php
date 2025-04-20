<?php

namespace app\front_api\controller;

use app\front_api\lists\recharge\RechargeLists;
use app\front_api\logic\RechargeLogic;
use app\front_api\validate\RechargeValidate;

/**
 * 充值控制器
 * @class RechargeController
 * @package app\front_api\controller
 * @author LZH
 * @date 2025/2/19
 */
class RechargeController extends BaseApiController
{

    /**
     * 获取充值列表
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function lists()
    {
        return $this->dataLists(new RechargeLists());
    }

    /**
     * 充值
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function recharge()
    {
        $params = (new RechargeValidate())->post()->goCheck('recharge', [
            'user_id' => $this->userId,
            'terminal' => $this->userInfo['terminal'],
        ]);
        $result = RechargeLogic::recharge($params);
        if (false === $result) {
            return $this->fail(RechargeLogic::getError());
        }
        return $this->data($result);
    }

    /**
     * 充值配置
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/19
     */
    public function config()
    {
        return $this->data(RechargeLogic::config($this->userId));
    }
}