<?php
declare(strict_types=1);

namespace app\common\enum;

/**
 * @class RefundEnum
 * @package app\common\enum
 * @author LZH
 * @date 2025/2/18
 */
class RefundEnum
{

    // 退款类型
    const TYPE_ADMIN = 1;  // 后台退款

    // 退款状态
    const REFUND_ING = 0;//退款中
    const REFUND_SUCCESS = 1;//退款成功
    const REFUND_ERROR = 2;//退款失败

    // 退款方式
    const REFUND_ONLINE = 1; // 线上退款
    const REFUND_OFFLINE = 2; // 线下退款


    // 退款订单类型
    const ORDER_TYPE_ORDER = 'order'; // 普通订单
    const ORDER_TYPE_RECHARGE = 'recharge'; // 充值订单

    /**
     * 退款类型描述
     * @param bool $value
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getTypeDesc(bool $value = true): array|string
    {
        $data = [
            self::TYPE_ADMIN  => '后台退款',
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value];
    }


    /**
     * 退款状态
     * @param bool $value
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getStatusDesc(bool $value = true): array|string
    {
        $data = [
            self::REFUND_ING  => '退款中',
            self::REFUND_SUCCESS  => '退款成功',
            self::REFUND_ERROR  => '退款失败',
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value];
    }

    /**
     * 退款方式
     * @param bool $value
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getWayDesc(bool $value = true): array|string
    {
        $data = [
            self::REFUND_ONLINE  => '线上退款',
            self::REFUND_OFFLINE  => '线下退款',
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value];
    }

    /**
     * 通过支付方式获取退款方式
     * @param int $payWay
     * @return int
     * @author LZH
     * @date 2025/2/18
     */
    public static function getRefundWayByPayWay(int $payWay): int
    {
        if (in_array($payWay, [PayEnum::ALI_PAY, PayEnum::WECHAT_PAY])) {
            return self::REFUND_ONLINE;
        }
        return self::REFUND_OFFLINE;
    }

}