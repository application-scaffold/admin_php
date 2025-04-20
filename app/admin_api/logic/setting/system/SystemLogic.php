<?php
declare(strict_types=1);

namespace app\admin_api\logic\setting\system;

use app\common\logic\BaseLogic;

/**
 * @class SystemLogic
 * @package app\admin_api\logic\setting\system
 * @author LZH
 * @date 2025/2/19
 */
class SystemLogic extends BaseLogic
{

    /**
     * 系统环境信息
     * @return array[]
     * @author LZH
     * @date 2025/2/19
     */
    public static function getInfo() : array
    {
        $server = [
            ['param' => '服务器操作系统', 'value' => PHP_OS],
            ['param' => 'web服务器环境', 'value' => $_SERVER['SERVER_SOFTWARE']],
            ['param' => 'PHP版本', 'value' => PHP_VERSION],
        ];

        $env = [
            [   'option' => 'PHP版本',
                'require' => '8.0版本以上',
                'status' => (int)compare_php('8.0.0'),
                'remark' => ''
            ]
        ];

        $auth = [
            [
                'dir' => '/runtime',
                'require' => 'runtime目录可写',
                'status' => (int)check_dir_write('runtime'),
                'remark' => ''
            ],
        ];

        return [
            'server' => $server,
            'env' => $env,
            'auth' => $auth,
        ];
    }

}