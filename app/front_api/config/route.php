<?php
declare(strict_types=1);

return [
    'middleware' => [
        app\front_api\http\middleware\InitMiddleware::class, // 初始化
        app\front_api\http\middleware\LoginMiddleware::class, // 登录验证
    ],
];
