<?php

declare (strict_types=1);

namespace app\common\http\middleware;

use app\common\service\JsonService;
use Closure;

/**
 * 自定义跨域中间件
 * @class CorsAllowMiddleware
 * @package app\common\http\middleware
 * @author LZH
 * @date 2025/2/18
 */
class CorsAllowMiddleware
{
    /**
     * 允许的请求头常量
     */
    private const ALLOWED_HEADERS = [
        'Authorization', 'Sec-Fetch-Mode', 'DNT', 'X-Mx-ReqToken', 'Keep-Alive', 'User-Agent',
        'If-Match', 'If-None-Match', 'If-Unmodified-Since', 'X-Requested-With', 'If-Modified-Since',
        'Cache-Control', 'Content-Type', 'Accept-Language', 'Origin', 'Accept-Encoding', 'Access-Token',
        'token', 'version'
    ];

    /**
     * 跨域处理
     * @param $request
     * @param Closure $next
     * @param array|null $header
     * @return mixed
     * @author LZH
     * @date 2025/2/18
     */
    public function handle($request, Closure $next, ?array $header = []): mixed
    {
        // 设置跨域头
        $this->setCorsHeaders();

        // 如果是OPTIONS请求，直接返回响应
        if (strtoupper($request->method()) === 'OPTIONS') {
            return response();
        }

        // 安装检测
        $install = file_exists(root_path() . '/config/install.lock');
        if (!$install) {
            return JsonService::fail('程序未安装', [], -2);
        }

        return $next($request);
    }

    /**
     * 设置跨域头信息
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    private function setCorsHeaders(): void
    {
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Headers'     => implode(', ', self::ALLOWED_HEADERS),
            'Access-Control-Allow-Methods'     => 'GET, POST, PATCH, PUT, DELETE, post',
            'Access-Control-Max-Age'           => '1728000',
            'Access-Control-Allow-Credentials' => 'true'
        ];

        foreach ($headers as $key => $value) {
            header("$key: $value");
        }
    }
}