<?php

declare (strict_types=1);

namespace app\common\exception;

use think\Exception;

/**
 * 控制器继承异常
 * @class ControllerExtendException
 * @package app\common\exception
 * @author LZH
 * @date 2025/2/18
 */
class ControllerExtendException extends Exception
{
    private string $model;

    /**
     * 构造方法
     * @access public
     * @param string $message
     * @param string $model
     */
    public function __construct(string $message, string $model = '')
    {
        $this->message = '控制器需要继承模块的基础控制器：' . $message;
        $this->model = $model;
    }
}