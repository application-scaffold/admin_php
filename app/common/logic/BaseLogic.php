<?php
declare(strict_types=1);

namespace app\common\logic;

/**
 * 逻辑基类
 * @class BaseLogic
 * @package app\common\logic
 * @author LZH
 * @date 2025/2/18
 */
class BaseLogic
{
    /**
     * 错误信息
     * @var string
     */
    protected static string $error;

    /**
     * 返回状态码
     * @var int
     */
    protected static int $returnCode = 0;

    protected static mixed $returnData;

    /**
     * 获取错误信息
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public static function getError() : string
    {
        if (false === self::hasError()) {
            return '系统错误';
        }
        return self::$error;
    }

    /**
     * 设置错误信息
     * @param string $error
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public static function setError(string $error) : void
    {
        !empty($error) && self::$error = $error;
    }


    /**
     * 是否存在错误
     * @return bool
     * @author LZH
     * @date 2025/2/18
     */
    public static function hasError() : bool
    {
        return !empty(self::$error);
    }


    /**
     * 设置状态码
     * @param $code
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public static function setReturnCode($code) : void
    {
        self::$returnCode = $code;
    }


    /**
     * 特殊场景返回指定状态码,默认为0
     * @return int
     * @author LZH
     * @date 2025/2/18
     */
    public static function getReturnCode() : int
    {
        return self::$returnCode;
    }


    /**
     * 获取内容
     * @return mixed
     * @author LZH
     * @date 2025/2/18
     */
    public static function getReturnData(): mixed
    {
        return self::$returnData;
    }

}