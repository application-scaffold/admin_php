<?php
declare(strict_types=1);

namespace app\common\enum;

class DefaultEnum
{
    //默认排序
    const SORT = 50;

    //显示隐藏
    const HIDE = 0;//隐藏
    const SHOW = 1;//显示

    //性别
    const UNKNOWN = 0;//未知
    const MAN = 1;//男
    const WOMAN = 2;//女

    //属性
    const SYSTEM = 1;//系统默认
    const CUSTOM = 2;//自定义

    /**
     * 获取显示状态
     * @param mixed $value
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getShowDesc(mixed $value = true): array|string
    {
        $data = [
            self::HIDE => '隐藏',
            self::SHOW => '显示'
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value];
    }

    /**
     * 启用状态
     * @param mixed $value
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getEnableDesc(mixed $value = true): array|string
    {
        $data = [
            self::HIDE => '停用',
            self::SHOW => '启用'
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value];
    }

    /**
     * 性别
     * @param mixed $value
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getSexDesc(mixed $value = true): array|string
    {
        $data = [
            self::UNKNOWN => '未知',
            self::MAN => '男',
            self::WOMAN => '女'
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value];
    }

    /**
     * 属性
     * @param mixed $value
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getAttrDesc(mixed $value = true): array|string
    {
        $data = [
            self::SYSTEM => '系统默认',
            self::CUSTOM => '自定义'
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value];
    }

    /**
     * 是否推荐
     * @param mixed $value
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getRecommendDesc(mixed $value = true): array|string
    {
        $data = [
            self::HIDE => '不推荐',
            self::SHOW => '推荐'
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value];
    }
}