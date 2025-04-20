<?php

namespace app\common\enum;

/**
 * 通过枚举类，枚举只有两个值的时候使用
 * @class YesNoEnum
 * @package app\common\enum
 * @author LZH
 * @date 2025/2/18
 */
class YesNoEnum
{
    const YES = 1;
    const NO = 0;

    /**
     * 获取禁用状态
     * @param $value
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getDisableDesc($value = true)
    {
        $data = [
            self::YES => '禁用',
            self::NO => '正常'
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value];
    }
}