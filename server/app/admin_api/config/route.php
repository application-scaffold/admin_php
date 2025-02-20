<?php

return [
    'middleware' => [
        // 初始化
        app\admin_api\http\middleware\InitMiddleware::class,
        // 登录验证
        app\admin_api\http\middleware\LoginMiddleware::class,
        // 权限认证
        app\admin_api\http\middleware\AuthMiddleware::class,
        // 演示模式 - 禁止提交数据
        app\admin_api\http\middleware\CheckDemoMiddleware::class,
        // 演示模式 - 不返回敏感数据
        app\admin_api\http\middleware\EncryDemoDataMiddleware::class,
    ],
];
