<?php

declare (strict_types=1);

namespace app\common\http\middleware;

use think\Request;

/**
 * 基础中间件
 * @class BaseMiddleware
 * @package app\common\http\middleware
 * @author LZH
 * @date 2025/2/18
 */
class BaseMiddleware
{
    public function handle(Request $request, \Closure $next) : mixed
    {
        return $next($request);
    }
}