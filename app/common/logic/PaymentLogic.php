<?php
declare(strict_types=1);

namespace app\common\logic;

use app\common\enum\PayEnum;
use app\common\enum\YesNoEnum;
use app\common\model\pay\PayWay;
use app\common\model\recharge\RechargeOrder;
use app\common\model\user\User;
use app\common\service\pay\AliPayService;
use app\common\service\pay\WeChatPayService;
use think\Model;

/**
 * 支付逻辑
 * @class PaymentLogic
 * @package app\common\logic
 * @author LZH
 * @date 2025/2/18
 */
class PaymentLogic extends BaseLogic
{

    /**
     * 支付方式
     * @param int $userId
     * @param int $terminal
     * @param array $params
     * @return array|false
     * @author LZH
     * @date 2025/2/18
     */
    public static function getPayWay(int $userId, int $terminal, array $params): bool|array
    {
        try {
            if ($params['from'] == 'recharge') {
                // 充值
                $order = RechargeOrder::findOrEmpty($params['order_id'])->toArray();
            }

            if (empty($order)) {
                throw new \Exception('待支付订单不存在');
            }

            //获取支付场景
            $pay_way = PayWay::alias('pw')
                ->join('dev_pay_config dp', 'pw.pay_config_id = dp.id')
                ->where(['pw.scene' => $terminal, 'pw.status' => YesNoEnum::YES])
                ->field('dp.id,dp.name,dp.pay_way,dp.icon,dp.sort,dp.remark,pw.is_default')
                ->order('pw.is_default desc,dp.sort desc,id asc')
                ->select()
                ->toArray();

            foreach ($pay_way as $k => &$item) {
                if ($item['pay_way'] == PayEnum::WECHAT_PAY) {
                    $item['extra'] = '微信快捷支付';
                }
                if ($item['pay_way'] == PayEnum::ALI_PAY) {
                    $item['extra'] = '支付宝快捷支付';
                }
                if ($item['pay_way'] == PayEnum::BALANCE_PAY) {
                    $user_money = User::where(['id' => $userId])->value('user_money');
                    $item['extra'] = '可用余额:' . $user_money;
                }
                // 充值时去除余额支付
                if ($params['from'] == 'recharge' && $item['pay_way'] == PayEnum::BALANCE_PAY) {
                    unset($pay_way[$k]);
                }
            }

            return [
                'lists' => array_values($pay_way),
                'order_amount' => $order['order_amount'],
            ];

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * 获取支付状态
     * @param array $params
     * @return array|false
     * @author LZH
     * @date 2025/2/18
     */
    public static function getPayStatus(array $params): bool|array
    {
        try {
            $order = [];
            $orderInfo = [];
            switch ($params['from']) {
                case 'recharge':
                    $order = RechargeOrder::where(['user_id' => $params['user_id'], 'id' => $params['order_id']])
                        ->findOrEmpty();
                    $payTime = empty($order['pay_time']) ? '' : date('Y-m-d H:i:s', $order['pay_time']);
                    $orderInfo = [
                        'order_id' => $order['id'],
                        'order_sn' => $order['sn'],
                        'order_amount' => $order['order_amount'],
                        'pay_way' => PayEnum::getPayDesc($order['pay_way']),
                        'pay_status' => PayEnum::getPayStatusDesc($order['pay_status']),
                        'pay_time' => $payTime,
                    ];
                    break;
            }

            if (empty($order)) {
                throw new \Exception('订单不存在');
            }

            return [
                'pay_status' => $order['pay_status'],
                'pay_way' => $order['pay_way'],
                'order' => $orderInfo
            ];
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * 获取预支付订单信息
     * @param array $params
     * @return RechargeOrder|array|false|mixed|Model
     * @author LZH
     * @date 2025/2/18
     */
    public static function getPayOrderInfo(array $params): mixed
    {
        try {
            switch ($params['from']) {
                case 'recharge':
                    $order = RechargeOrder::findOrEmpty($params['order_id']);
                    if ($order->isEmpty()) {
                        throw new \Exception('充值订单不存在');
                    }
                    break;
            }

            if ($order['pay_status'] == PayEnum::ISPAID) {
                throw new \Exception('订单已支付');
            }
            return $order;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 支付
     * @param int $payWay
     * @param string $from
     * @param array $order
     * @param string $terminal
     * @param string $redirectUrl
     * @return array|bool|mixed|string|string[]
     * @throws \Exception
     * @author LZH
     * @date 2025/2/18
     */
    public static function pay(int $payWay, string $from, array $order, string $terminal, string $redirectUrl): mixed
    {
        // 支付编号-仅为微信支付预置(同一商户号下不同客户端支付需使用唯一订单号)
        $paySn = $order['sn'];
        if ($payWay == PayEnum::WECHAT_PAY) {
            $paySn = self::formatOrderSn($order['sn'], $terminal);
        }

        //更新支付方式
        switch ($from) {
            case 'recharge':
                RechargeOrder::update(['pay_way' => $payWay, 'pay_sn' => $paySn], ['id' => $order['id']]);
                break;
        }

        if ($order['order_amount'] == 0) {
            PayNotifyLogic::handle($from, $order['sn']);
            return ['pay_way' => PayEnum::BALANCE_PAY];
        }

        $payService = null;
        switch ($payWay) {
            case PayEnum::WECHAT_PAY:
                $payService = (new WeChatPayService($terminal, $order['user_id'] ?? null));
                $order['pay_sn'] = $paySn;
                $order['redirect_url'] = $redirectUrl;
                $result = $payService->pay($from, $order);
                break;
            case PayEnum::ALI_PAY:
                $payService = (new AliPayService($terminal));
                $order['redirect_url'] = $redirectUrl;
                $result = $payService->pay($from, $order);
                break;
            default:
                self::$error = '订单异常';
                $result = false;
        }

        if (false === $result && !self::hasError()) {
            self::setError($payService->getError());
        }
        return $result;
    }

    /**
     * 设置订单号 支付回调时截取前面的单号 18个
     * 回调时使用了不同的回调地址,导致跨客户端支付时(例如小程序,公众号)可能出现201,商户订单号重复错误
     * @param string $orderSn
     * @param string $terminal
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public static function formatOrderSn(string $orderSn, string $terminal): string
    {
        $suffix = mb_substr(time(), -4);
        return $orderSn . $terminal . $suffix;
    }

}