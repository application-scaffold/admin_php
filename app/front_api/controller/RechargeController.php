<?php
declare(strict_types=1);

namespace app\front_api\controller;

use app\front_api\lists\recharge\RechargeLists;
use app\front_api\logic\RechargeLogic;
use app\front_api\validate\RechargeValidate;
use think\response\Json;

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
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): Json
    {
        return $this->dataLists(new RechargeLists());
    }

    /**
     * 充值
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function recharge(): Json
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
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function config(): Json
    {
        return $this->data(RechargeLogic::config($this->userId));
    }
}