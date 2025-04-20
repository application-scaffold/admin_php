<?php
declare(strict_types=1);

return [
    'listen' => [
        'AppInit' => [],
        'HttpRun' => [],
        'HttpEnd' => ['app\admin_api\listener\OperationLog'],
        'LogLevel' => [],
        'LogWrite' => [],
    ]
];