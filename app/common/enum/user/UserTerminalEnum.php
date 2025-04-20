<?php
declare(strict_types=1);

namespace app\common\enum\user;

/**
 * 管理后台登录终端
 * @class UserTerminalEnum
 * @package app\common\enum\user
 * @author LZH
 * @date 2025/2/18
 */
class UserTerminalEnum
{
    //const OTHER = 0;    //其他来源
    const WECHAT_MMP = 1; //微信小程序
    const WECHAT_OA  = 2; //微信公众号
    const H5         = 3; //手机H5登录
    const PC         = 4; //电脑PC
    const IOS        = 5; //苹果app
    const ANDROID    = 6; //安卓app


    const ALL_TERMINAL = [
        self::WECHAT_MMP,
        self::WECHAT_OA,
        self::H5,
        self::PC,
        self::IOS,
        self::ANDROID,
    ];

    /**
     * 获取终端
     * @param bool $from
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getTermInalDesc(bool $from = true): array|string
    {
        $desc = [
            self::WECHAT_MMP    => '微信小程序',
            self::WECHAT_OA     => '微信公众号',
            self::H5            => '手机H5',
            self::PC            => '电脑PC',
            self::IOS           => '苹果APP',
            self::ANDROID       => '安卓APP',
        ];
        if(true === $from){
            return $desc;
        }
        return $desc[$from] ?? '';
    }
}