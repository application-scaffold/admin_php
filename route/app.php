<?php
declare(strict_types=1);

use think\facade\Console;
use think\facade\Route;
use OpenApi\Generator;

// 管理后台
Route::rule('admin/:any', function () {
    return view(app()->getRootPath() . 'public/admin/index.html');
})->pattern(['any' => '\w+']);

// 手机端
Route::rule('mobile/:any', function () {
    return view(app()->getRootPath() . 'public/mobile/index.html');
})->pattern(['any' => '\w+']);

// PC端
Route::rule('pc/:any', function () {
    return view(app()->getRootPath() . 'public/pc/index.html');
})->pattern(['any' => '\w+']);

//定时任务
Route::rule('crontab', function () {
    Console::call('crontab');
});

// routes/api.php
Route::group('doc', function () {
    Route::get('generate/front', function () {
        // 生成前端文档
        $openapi = Generator::scan([app_path('front_api/controller')]);

        // 确保目录存在
        $outputDir = public_path('swagger-ui');
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // 保存为文件
        $filePath = $outputDir . '/front.json';
        $openapi->saveAs($filePath, 'json');

        return json(['status' => 'success', 'path' => $filePath]);
    });

    Route::get('generate/admin', function () {
        // 生成管理端文档
        $openapi = Generator::scan([app_path('admin_api/controller')]);

        $outputDir = public_path('swagger-ui');
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $filePath = $outputDir . '/admin.json';
        $openapi->saveAs($filePath, 'json');

        return json(['status' => 'success', 'path' => $filePath]);
    });
});
