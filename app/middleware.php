<?php

declare (strict_types = 1);

// 全局中间件定义文件
return [
    // 全局请求缓存
    // \think\middleware\CheckRequestCache::class,
    // 多语言加载
    // \think\middleware\LoadLangPack::class,
    // Session初始化
    // \think\middleware\SessionInit::class
    //跨域中间件
    app\common\http\middleware\CorsAllowMiddleware::class,
    //基础中间件
    app\common\http\middleware\BaseMiddleware::class,
];
