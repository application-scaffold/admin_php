<?php
declare(strict_types=1);

namespace app\common\enum\notice;

/**
 * 短信枚举
 * @class SmsEnum
 * @package app\common\enum\notice
 * @author LZH
 * @date 2025/2/18
 */
class SmsEnum
{
    /**
     * 发送状态
     */
    const SEND_ING = 0;
    const SEND_SUCCESS = 1;
    const SEND_FAIL = 2;

    /**
     * 短信平台
     */
    const ALI = 1;
    const TENCENT = 2;


    /**
     * 获取短信平台名称
     * @param mixed $value
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public static function getNameDesc(mixed $value): string
    {
        $desc = [
            'ALI' => '阿里云短信',
            'TENCENT' => '腾讯云短信',
        ];
        return $desc[$value] ?? '';
    }

}