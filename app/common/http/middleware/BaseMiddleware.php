<?php

declare (strict_types=1);

namespace app\common\http\middleware;

/**
 * 基础中间件
 * @class BaseMiddleware
 * @package app\common\http\middleware
 * @author LZH
 * @date 2025/2/18
 */
class BaseMiddleware
{
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }
}