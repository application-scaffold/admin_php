<?php
declare(strict_types=1);

namespace app\admin_api\logic\recharge;


use app\common\enum\RefundEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\AccountLogLogic;
use app\common\logic\BaseLogic;
use app\common\logic\RefundLogic;
use app\common\model\recharge\RechargeOrder;
use app\common\model\refund\RefundRecord;
use app\common\model\user\User;
use app\common\service\ConfigService;
use think\facade\Db;


/**
 * 充值逻辑层
 * @class RechargeLogic
 * @package app\admin_api\logic\recharge
 * @author LZH
 * @date 2025/2/19
 */
class RechargeLogic extends BaseLogic
{

    /**
     * 获取充值设置
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getConfig(): array
    {
        $config = [
            'status' => ConfigService::get('recharge', 'status', 0),
            'min_amount' => ConfigService::get('recharge', 'min_amount', 0)
        ];

        return $config;
    }

    /**
     * 充值设置
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function setConfig(array $params): bool
    {
        try {
            if (isset($params['status'])) {
                ConfigService::set('recharge', 'status', $params['status']);
            }
            if (isset($params['min_amount'])) {
                ConfigService::set('recharge', 'min_amount', $params['min_amount']);
            }
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 退款
     * @param array $params
     * @param int $adminId
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function refund(array $params, int $adminId): array
    {
        Db::startTrans();
        try {
            $order = RechargeOrder::findOrEmpty($params['recharge_id']);

            // 更新订单信息, 标记已发起退款状态,具体退款成功看退款日志
            RechargeOrder::update([
                'id' => $order['id'],
                'refund_status' => YesNoEnum::YES,
            ]);

            // 更新用户余额及累计充值金额
            User::where(['id' => $order['user_id']])
                ->dec('total_recharge_amount', $order['order_amount'])
                ->dec('user_money', $order['order_amount'])
                ->update();

            // 记录日志
            AccountLogLogic::add(
                $order['user_id'],
                AccountLogEnum::UM_INC_ADMIN,
                AccountLogEnum::DEC,
                $order['order_amount'],
                $order['sn'],
                '充值订单退款'
            );

            // 生成退款记录
            $recordSn = generate_sn(RefundRecord::class, 'sn');
            $record = RefundRecord::create([
                'sn' => $recordSn,
                'user_id' => $order['user_id'],
                'order_id' => $order['id'],
                'order_sn' => $order['sn'],
                'order_type' => RefundEnum::ORDER_TYPE_RECHARGE,
                'order_amount' => $order['order_amount'],
                'refund_amount' => $order['order_amount'],
                'refund_type' => RefundEnum::TYPE_ADMIN,
                'transaction_id' => $order['transaction_id'] ?? '',
                'refund_way' => RefundEnum::getRefundWayByPayWay($order['pay_way']),
            ]);

            // 退款
            $result = RefundLogic::refund($order, $record['id'], $order['order_amount'], $adminId);

            $flag = true;
            $resultMsg = '操作成功';
            if ($result !== true) {
                $flag = false;
                $resultMsg = RefundLogic::getError();
            }

            Db::commit();
            return [$flag, $resultMsg];
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return [false, $e->getMessage()];
        }
    }

    /**
     * 重新退款
     * @param array $params
     * @param int $adminId
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function refundAgain(array $params, int $adminId): array
    {
        Db::startTrans();
        try {
            $record = RefundRecord::findOrEmpty($params['record_id']);
            $order = RechargeOrder::findOrEmpty($record['order_id']);

            // 退款
            $result = RefundLogic::refund($order, $record['id'], $order['order_amount'], $adminId);

            $flag = true;
            $resultMsg = '操作成功';
            if ($result !== true) {
                $flag = false;
                $resultMsg = RefundLogic::getError();
            }

            Db::commit();
            return [$flag, $resultMsg];
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return [false, $e->getMessage()];
        }
    }

}