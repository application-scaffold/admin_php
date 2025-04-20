<?php
declare(strict_types=1);

namespace app\common\enum;

/**
 * 支付
 * @class PayEnum
 * @package app\common\enum
 * @author LZH
 * @date 2025/2/18
 */
class PayEnum
{

    //支付类型
    const BALANCE_PAY   = 1; //余额支付
    const WECHAT_PAY    = 2; //微信支付
    const ALI_PAY       = 3; //支付宝支付


    //支付状态
    const UNPAID = 0; //未支付
    const ISPAID = 1; //已支付



    //支付场景
    const SCENE_H5 = 1;//H5
    const SCENE_OA = 2;//微信公众号
    const SCENE_MNP = 3;//微信小程序
    const SCENE_APP = 4;//APP
    const SCENE_PC = 5;//PC商城


    /**
     * 获取支付类型
     * @param bool $value
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getPayDesc(bool $value = true): array|string
    {
        $data = [
            self::BALANCE_PAY => '余额支付',
            self::WECHAT_PAY => '微信支付',
            self::ALI_PAY => '支付宝支付',
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value] ?? '';
    }


    /**
     * 支付状态
     * @param bool $value
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getPayStatusDesc(bool $value = true): array|string
    {
        $data = [
            self::UNPAID => '未支付',
            self::ISPAID => '已支付',
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value] ?? '';
    }

    /**
     * 支付场景
     * @param bool|string $value
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getPaySceneDesc(bool|string $value = true): array|string
    {
        $data = [
            self::SCENE_H5 => 'H5',
            self::SCENE_OA => '微信公众号',
            self::SCENE_MNP => '微信小程序',
            self::SCENE_APP => 'APP',
            self::SCENE_PC => 'PC',
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value] ?? '';
    }

}