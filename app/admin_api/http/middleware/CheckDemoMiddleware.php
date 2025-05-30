<?php

declare (strict_types=1);

namespace app\admin_api\http\middleware;

use app\common\service\JsonService;
use think\Request;

/**
 * 校验演示环境
 * @class CheckDemoMiddleware
 * @package app\admin_api\http\middleware
 * @author LZH
 * @date 2025/2/19
 */
class CheckDemoMiddleware
{

    // 允许post的接口
    protected array $ablePost = [
        'login/account',
        'login/logout',
    ];

    public function handle(Request $request, \Closure $next)
    {
        if ($request->method() != 'POST') {
            return $next($request);
        }

        $accessUri = strtolower($request->controller() . '/' . $request->action());
        if (!in_array($accessUri, $this->ablePost) && env('project.demo_env')) {
            return JsonService::fail('演示环境不支持修改数据，请下载源码本地部署体验');
        }

        return $next($request);
    }

}