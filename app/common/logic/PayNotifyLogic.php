<?php
declare(strict_types=1);

namespace app\common\logic;

use app\common\enum\PayEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\model\recharge\RechargeOrder;
use app\common\model\user\User;
use think\facade\Db;
use think\facade\Log;

/**
 * 支付成功后处理订单状态
 * @class PayNotifyLogic
 * @package app\common\logic
 * @author LZH
 * @date 2025/2/18
 */
class PayNotifyLogic extends BaseLogic
{

    /**
     * 使用数据库事务执行回调
     * @param string $action
     * @param string $orderSn
     * @param array $extra
     * @return bool|string
     * @author LZH
     * @date 2025/4/20
     */
    public static function handle(string $action, string $orderSn, array $extra = []): bool|string
    {
        Db::startTrans();
        try {
            self::$action($orderSn, $extra);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            Log::write(implode('-', [
                __CLASS__,
                __FUNCTION__,
                $e->getFile(),
                $e->getLine(),
                $e->getMessage()
            ]));
            self::setError($e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * 充值回调
     * @param string $orderSn
     * @param array $extra
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public static function recharge(string $orderSn, array $extra = []): void
    {
        $order = RechargeOrder::where('sn', $orderSn)->findOrEmpty();
        // 增加用户累计充值金额及用户余额
        $user = User::findOrEmpty($order->user_id);
        $user->total_recharge_amount += $order->order_amount;
        $user->user_money += $order->order_amount;
        $user->save();

        // 记录账户流水
        AccountLogLogic::add(
            $order->user_id,
            AccountLogEnum::UM_INC_RECHARGE,
            AccountLogEnum::INC,
            $order->order_amount,
            $order->sn,
            '用户充值'
        );

        // 更新充值订单状态
        $order->transaction_id = $extra['transaction_id'] ?? '';
        $order->pay_status = PayEnum::ISPAID;
        $order->pay_time = time();
        $order->save();
    }

}
