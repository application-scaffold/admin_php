<?php
declare(strict_types=1);

namespace app\admin_api\logic\setting;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;
use think\facade\Cache;


/**
 * 存储设置逻辑层
 * @class StorageLogic
 * @package app\admin_api\logic\setting
 * @author LZH
 * @date 2025/2/19
 */
class StorageLogic extends BaseLogic
{

    /**
     * 存储引擎列表
     * @return array[]
     * @author LZH
     * @date 2025/2/19
     */
    public static function lists(): array
    {

        $default = ConfigService::get('storage', 'default', 'local');

        $data = [
            [
                'name' => '本地存储',
                'path' => '存储在本地服务器',
                'engine' => 'local',
                'status' => $default == 'local' ? 1 : 0
            ],
            [
                'name' => '七牛云存储',
                'path' => '存储在七牛云，请前往七牛云开通存储服务',
                'engine' => 'qiniu',
                'status' => $default == 'qiniu' ? 1 : 0
            ],
            [
                'name' => '阿里云OSS',
                'path' => '存储在阿里云，请前往阿里云开通存储服务',
                'engine' => 'aliyun',
                'status' => $default == 'aliyun' ? 1 : 0
            ],
            [
                'name' => '腾讯云COS',
                'path' => '存储在腾讯云，请前往腾讯云开通存储服务',
                'engine' => 'qcloud',
                'status' => $default == 'qcloud' ? 1 : 0
            ]
        ];
        return $data;
    }


    /**
     * 存储设置详情
     * @param array $param
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public static function detail(array $param): mixed
    {

        $default = ConfigService::get('storage', 'default', '');

        // 本地存储
        $local = ['status' => $default == 'local' ? 1 : 0];
        // 七牛云存储
        $qiniu = ConfigService::get('storage', 'qiniu', [
            'bucket' => '',
            'access_key' => '',
            'secret_key' => '',
            'domain' => '',
            'status' => $default == 'qiniu' ? 1 : 0
        ]);

        // 阿里云存储
        $aliyun = ConfigService::get('storage', 'aliyun', [
            'bucket' => '',
            'access_key' => '',
            'secret_key' => '',
            'domain' => '',
            'status' => $default == 'aliyun' ? 1 : 0
        ]);

        // 腾讯云存储
        $qcloud = ConfigService::get('storage', 'qcloud', [
            'bucket' => '',
            'region' => '',
            'access_key' => '',
            'secret_key' => '',
            'domain' => '',
            'status' => $default == 'qcloud' ? 1 : 0
        ]);

        $data = [
            'local' => $local,
            'qiniu' => $qiniu,
            'aliyun' => $aliyun,
            'qcloud' => $qcloud
        ];
        $result = $data[$param['engine']];
        if ($param['engine'] == $default) {
            $result['status'] = 1;
        } else {
            $result['status'] = 0;
        }
        return $result;
    }


    /**
     * 设置存储参数
     * @param array $params
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    public static function setup(array $params): bool|string
    {
        if ($params['status'] == 1) { //状态为开启
            ConfigService::set('storage', 'default', $params['engine']);
        } else {
            ConfigService::set('storage', 'default', 'local');
        }

        switch ($params['engine']) {
            case 'local':
                ConfigService::set('storage', 'local', []);
                break;
            case 'qiniu':
                ConfigService::set('storage', 'qiniu', [
                    'bucket' => $params['bucket'] ?? '',
                    'access_key' => $params['access_key'] ?? '',
                    'secret_key' => $params['secret_key'] ?? '',
                    'domain' => $params['domain'] ?? ''
                ]);
                break;
            case 'aliyun':
                ConfigService::set('storage', 'aliyun', [
                    'bucket' => $params['bucket'] ?? '',
                    'access_key' => $params['access_key'] ?? '',
                    'secret_key' => $params['secret_key'] ?? '',
                    'domain' => $params['domain'] ?? ''
                ]);
                break;
            case 'qcloud':
                ConfigService::set('storage', 'qcloud', [
                    'bucket' => $params['bucket'] ?? '',
                    'region' => $params['region'] ?? '',
                    'access_key' => $params['access_key'] ?? '',
                    'secret_key' => $params['secret_key'] ?? '',
                    'domain' => $params['domain'] ?? '',
                ]);
                break;
        }

        Cache::delete('STORAGE_DEFAULT');
        Cache::delete('STORAGE_ENGINE');
        if ($params['engine'] == 'local' && $params['status'] == 0) {
            return '默认开启本地存储';
        } else {
            return true;
        }
    }

    /**
     * 切换状态
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function change(array $params): void
    {
        $default = ConfigService::get('storage', 'default', '');
        if ($default == $params['engine']) {
            ConfigService::set('storage', 'default', 'local');
        } else {
            ConfigService::set('storage', 'default', $params['engine']);
        }
        Cache::delete('STORAGE_DEFAULT');
        Cache::delete('STORAGE_ENGINE');
    }
}