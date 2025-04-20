<?php
declare(strict_types=1);

namespace app\admin_api\logic\channel;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;

/**
 * App设置逻辑层
 * @class AppSettingLogic
 * @package app\admin_api\logic\channel
 * @author LZH
 * @date 2025/2/19
 */
class AppSettingLogic extends BaseLogic
{

    /**
     * 获取App设置
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getConfig(): array
    {
        $config = [
            'ios_download_url' => ConfigService::get('app', 'ios_download_url', ''),
            'android_download_url' => ConfigService::get('app', 'android_download_url', ''),
            'download_title' => ConfigService::get('app', 'download_title', ''),
        ];
        return $config;
    }


    /**
     * App设置
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function setConfig(array $params): void
    {
        ConfigService::set('app', 'ios_download_url', $params['ios_download_url'] ?? '');
        ConfigService::set('app', 'android_download_url', $params['android_download_url'] ?? '');
        ConfigService::set('app', 'download_title', $params['download_title'] ?? '');
    }
}