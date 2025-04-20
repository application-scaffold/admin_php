<?php
declare(strict_types=1);

namespace app\admin_api\validate\recharge;

use app\common\enum\PayEnum;
use app\common\enum\RefundEnum;
use app\common\enum\YesNoEnum;
use app\common\model\recharge\RechargeOrder;
use app\common\model\refund\RefundRecord;
use app\common\model\user\User;
use app\common\validate\BaseValidate;

/**
 * 充值退款校验
 * @class RechargeRefundValidate
 * @package app\admin_api\validate\recharge
 * @author LZH
 * @date 2025/2/19
 */
class RechargeRefundValidate extends BaseValidate
{
    protected $rule = [
        'recharge_id' => 'require|checkRecharge',
        'record_id' => 'require|checkRecord',
    ];

    protected $message = [
        'recharge_id.require' => '参数缺失',
        'record_id.require' => '参数缺失',
    ];


    public function sceneRefund(): RechargeRefundValidate
    {
        return $this->only(['recharge_id']);
    }


    public function sceneAgain(): RechargeRefundValidate
    {
        return $this->only(['record_id']);
    }


    /**
     * 校验充值订单能否发起退款
     * @param string $rechargeId
     * @param string $rule
     * @param array $data
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    protected function checkRecharge(string $rechargeId, string $rule, array $data)
    {
        $order = RechargeOrder::findOrEmpty($rechargeId);

        if ($order->isEmpty()) {
            return '充值订单不存在';
        }

        if ($order['pay_status'] != PayEnum::ISPAID) {
            return '当前订单不可退款';
        }

        // 校验订单是否已退款
        if ($order['refund_status'] == YesNoEnum::YES) {
            return '订单已发起退款,退款失败请到退款记录重新退款';
        }

        // 校验余额
        $user = User::findOrEmpty($order['user_id']);
        if ($user['user_money'] < $order['order_amount']) {
            return '退款失败:用户余额已不足退款金额';
        }

        return true;
    }

    /**
     * 校验退款记录
     * @param string $recordId
     * @param string $rule
     * @param array $data
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    protected function checkRecord(string $recordId, string $rule, array $data): bool|string
    {
        $record = RefundRecord::findOrEmpty($recordId);
        if ($record->isEmpty()) {
            return '退款记录不存在';
        }

        if($record['refund_status'] == RefundEnum::REFUND_SUCCESS) {
            return '该退款记录已退款成功';
        }

        $order = RechargeOrder::findOrEmpty($record['order_id']);
        $user = User::findOrEmpty($record['user_id']);

        if ($user['user_money'] < $order['order_amount']) {
            return '退款失败:用户余额已不足退款金额';
        }

        return true;
    }

}