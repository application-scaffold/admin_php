<?php
declare(strict_types=1);

namespace app\admin_api\logic\channel;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;


/**
 * H5设置逻辑层
 * @class WebPageSettingLogic
 * @package app\admin_api\logic\channel
 * @author LZH
 * @date 2025/2/19
 */
class WebPageSettingLogic extends BaseLogic
{
    /**
     * 获取H5设置
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getConfig(): array
    {
        $config = [
            // 渠道状态 0-关闭 1-开启
            'status' => ConfigService::get('web_page', 'status', 1),
            // 关闭后渠道后访问页面 0-空页面 1-自定义链接
            'page_status' => ConfigService::get('web_page', 'page_status', 0),
            // 自定义链接
            'page_url' => ConfigService::get('web_page', 'page_url', ''),
            'url' => request()->domain() . '/mobile'
        ];
        return $config;
    }

    /**
     * H5设置
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function setConfig(array $params): void
    {
        ConfigService::set('web_page', 'status', $params['status']);
        ConfigService::set('web_page', 'page_status', $params['page_status']);
        ConfigService::set('web_page', 'page_url', $params['page_url']);
    }
}