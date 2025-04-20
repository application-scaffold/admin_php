<?php

namespace app\common\model\recharge;

use app\common\enum\PayEnum;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 充值订单模型
 * @class RechargeOrder
 * @package app\common\model\recharge
 * @author LZH
 * @date 2025/2/18
 */
class RechargeOrder extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    /**
     * 支付方式
     * @param $value
     * @param $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getPayWayTextAttr($value, $data)
    {
        return PayEnum::getPayDesc($data['pay_way']);
    }

    /**
     * 支付状态
     * @param $value
     * @param $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getPayStatusTextAttr($value, $data)
    {
        return PayEnum::getPayStatusDesc($data['pay_status']);
    }
}