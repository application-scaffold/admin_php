<?php
declare(strict_types=1);

namespace app\admin_api\logic\channel;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;

/**
 * 微信开放平台
 * @class OpenSettingLogic
 * @package app\admin_api\logic\channel
 * @author LZH
 * @date 2025/2/19
 */
class OpenSettingLogic extends BaseLogic
{

    /**
     * 获取微信开放平台设置
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getConfig(): array
    {
        $config = [
            'app_id' => ConfigService::get('open_platform', 'app_id', ''),
            'app_secret' => ConfigService::get('open_platform', 'app_secret', ''),
        ];

        return $config;
    }


    /**
     * 微信开放平台设置
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function setConfig(array $params): void
    {
        ConfigService::set('open_platform', 'app_id', $params['app_id'] ?? '');
        ConfigService::set('open_platform', 'app_secret', $params['app_secret'] ?? '');
    }
}