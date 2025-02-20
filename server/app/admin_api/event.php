<?php

return [
    'listen' => [
        'AppInit' => [],
        'HttpRun' => [],
        'HttpEnd' => ['app\admin_api\listener\OperationLog'],
        'LogLevel' => [],
        'LogWrite' => [],
    ]
];