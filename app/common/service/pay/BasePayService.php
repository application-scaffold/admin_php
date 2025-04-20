<?php

declare(strict_types=1);

namespace app\common\service\pay;

use think\facade\Log;

class BasePayService
{
    /**
     * 错误信息
     * @var string
     */
    protected string $error;

    /**
     * 返回状态码
     * @var int
     */
    protected int $returnCode = 0;


    /**
     * 获取错误信息
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getError(): string
    {
        if (false === self::hasError()) {
            return '系统错误';
        }
        return $this->error;
    }

    /**
     * 设置错误信息
     * @param $error
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public function setError($error): void
    {
        $this->error = $error;
    }


    /**
     * 是否存在错误
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public function hasError(): bool
    {
        return !empty($this->error);
    }


    /**
     * 设置状态码
     * @param $code
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public function setReturnCode($code): void
    {
        $this->returnCode = $code;
    }


    /**
     * 特殊场景返回指定状态码,默认为0
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function getReturnCode(): int
    {
        return $this->returnCode;
    }

}