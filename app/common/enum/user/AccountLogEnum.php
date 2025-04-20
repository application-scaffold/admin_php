<?php
declare(strict_types=1);

namespace app\common\enum\user;

/**
 * 用户账户流水变动表枚举
 * @class AccountLogEnum
 * @package app\common\enum\user
 * @author LZH
 * @date 2025/2/18
 */
class AccountLogEnum
{
    /**
     * 变动类型命名规则：对象_动作_简洁描述
     * 动作 DEC-减少 INC-增加
     * 对象 UM-用户余额
     */

    /**
     * 变动对象
     * UM 用户余额(user_money)
     */
    const UM = 1;

    /**
     * 动作
     * INC 增加
     * DEC 减少
     */
    const INC = 1;
    const DEC = 2;


    /**
     * 用户余额减少类型
     */
    const UM_DEC_ADMIN = 100;
    const UM_DEC_RECHARGE_REFUND = 101;

    /**
     * 用户余额增加类型
     */
    const UM_INC_ADMIN = 200;
    const UM_INC_RECHARGE = 201;


    /**
     * 用户余额（减少类型汇总）
     */
    const UM_DEC = [
        self::UM_DEC_ADMIN,
        self::UM_DEC_RECHARGE_REFUND,
    ];


    /**
     * 用户余额（增加类型汇总）
     */
    const UM_INC = [
        self::UM_INC_ADMIN,
        self::UM_INC_RECHARGE,
    ];

    /**
     * 动作描述
     * @param string $action
     * @param bool $flag
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getActionDesc(string $action, bool $flag = false): array|string
    {
        $desc = [
            self::DEC => '减少',
            self::INC => '增加',
        ];
        if ($flag) {
            return $desc;
        }
        return $desc[$action] ?? '';
    }

    /**
     * 变动类型描述
     * @param string $changeType
     * @param bool $flag
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getChangeTypeDesc(string $changeType, bool $flag = false): array|string
    {
        $desc = [
            self::UM_DEC_ADMIN => '平台减少余额',
            self::UM_INC_ADMIN => '平台增加余额',
            self::UM_INC_RECHARGE => '充值增加余额',
            self::UM_DEC_RECHARGE_REFUND => '充值订单退款减少余额',
        ];
        if ($flag) {
            return $desc;
        }
        return $desc[$changeType] ?? '';
    }


    /**
     * 获取用户余额类型描述
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getUserMoneyChangeTypeDesc(): array|string
    {
        $UMChangeType = self::getUserMoneyChangeType();
        $changeTypeDesc = self::getChangeTypeDesc('', true);
        return array_filter($changeTypeDesc, function ($key) use ($UMChangeType) {
            return in_array($key, $UMChangeType);
        }, ARRAY_FILTER_USE_KEY);
    }


    /**
     * 获取用户余额变动类型
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public static function getUserMoneyChangeType() : array
    {
        return array_merge(self::UM_DEC, self::UM_INC);
    }


    /**
     * 获取变动对象
     * @param int $changeType
     * @return false|int
     * @author LZH
     * @date 2025/2/18
     */
    public static function getChangeObject(int $changeType): bool|int
    {
        // 用户余额
        $um = self::getUserMoneyChangeType();
        if (in_array($changeType, $um)) {
            return self::UM;
        }

        // 其他...

        return false;
    }
}