<?php

namespace app\front_api\validate;

use app\common\enum\PayEnum;
use app\common\service\ConfigService;
use app\common\validate\BaseValidate;

/**
 * 用户验证器
 * @class RechargeValidate
 * @package app\front_api\validate
 * @author LZH
 * @date 2025/2/20
 */
class RechargeValidate extends BaseValidate
{

    protected $rule = [
        'money' => 'require|gt:0|checkMoney',
    ];


    protected $message = [
        'money.require' => '请填写充值金额',
        'money.gt' => '请填写大于0的充值金额',
    ];


    public function sceneRecharge()
    {
        return $this->only(['money']);
    }

    /**
     * 校验金额
     * @param $money
     * @param $rule
     * @param $data
     * @return string|true
     * @author LZH
     * @date 2025/2/20
     */
    protected function checkMoney($money, $rule, $data)
    {
        $status = ConfigService::get('recharge', 'status', 0);
        if (!$status) {
            return '充值功能已关闭';
        }

        $minAmount = ConfigService::get('recharge', 'min_amount', 0);

        if ($money < $minAmount) {
            return '最低充值金额' . $minAmount . "元";
        }

        return true;
    }

}