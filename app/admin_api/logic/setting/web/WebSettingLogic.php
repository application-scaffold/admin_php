<?php
declare(strict_types=1);

namespace app\admin_api\logic\setting\web;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;
use app\common\service\FileService;

/**
 * 网站设置
 * @class WebSettingLogic
 * @package app\admin_api\logic\setting\web
 * @author LZH
 * @date 2025/2/19
 */
class WebSettingLogic extends BaseLogic
{

    /**
     * 获取网站信息
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getWebsiteInfo(): array
    {
        return [
            'name' => ConfigService::get('website', 'name'),
            'web_favicon' => FileService::getFileUrl(ConfigService::get('website', 'web_favicon')),
            'web_logo' => FileService::getFileUrl(ConfigService::get('website', 'web_logo')),
            'login_image' => FileService::getFileUrl(ConfigService::get('website', 'login_image')),
            'shop_name' => ConfigService::get('website', 'shop_name'),
            'shop_logo' => FileService::getFileUrl(ConfigService::get('website', 'shop_logo')),

            'pc_logo' => FileService::getFileUrl(ConfigService::get('website', 'pc_logo')),
            'pc_title' => ConfigService::get('website', 'pc_title', ''),
            'pc_ico' => FileService::getFileUrl(ConfigService::get('website', 'pc_ico')),
            'pc_desc' => ConfigService::get('website', 'pc_desc', ''),
            'pc_keywords' => ConfigService::get('website', 'pc_keywords', ''),

            'h5_favicon' => FileService::getFileUrl(ConfigService::get('website', 'h5_favicon')),
        ];
    }


    /**
     * 设置网站信息
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function setWebsiteInfo(array $params): void
    {
        $h5favicon = FileService::setFileUrl($params['h5_favicon']);
        $favicon = FileService::setFileUrl($params['web_favicon']);
        $logo = FileService::setFileUrl($params['web_logo']);
        $login = FileService::setFileUrl($params['login_image']);
        $shopLogo = FileService::setFileUrl($params['shop_logo']);
        $pcLogo = FileService::setFileUrl($params['pc_logo']);
        $pcIco = FileService::setFileUrl($params['pc_ico'] ?? '');

        ConfigService::set('website', 'name', $params['name']);
        ConfigService::set('website', 'web_favicon', $favicon);
        ConfigService::set('website', 'web_logo', $logo);
        ConfigService::set('website', 'login_image', $login);
        ConfigService::set('website', 'shop_name', $params['shop_name']);
        ConfigService::set('website', 'shop_logo', $shopLogo);
        ConfigService::set('website', 'pc_logo', $pcLogo);

        ConfigService::set('website', 'pc_title', $params['pc_title']);
        ConfigService::set('website', 'pc_ico', $pcIco);
        ConfigService::set('website', 'pc_desc', $params['pc_desc'] ?? '');
        ConfigService::set('website', 'pc_keywords', $params['pc_keywords'] ?? '');

        ConfigService::set('website', 'h5_favicon', $h5favicon);
    }

    /**
     * 获取版权备案
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getCopyright() : array
    {
        return ConfigService::get('copyright', 'config', []);
    }


    /**
     * 设置版权备案
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function setCopyright(array $params): bool
    {
        try {
            if (!is_array($params['config'])) {
                throw new \Exception('参数异常');
            }
            ConfigService::set('copyright', 'config', $params['config'] ?? []);
            return true;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }


    /**
     * 设置政策协议
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function setAgreement(array $params): void
    {
        $serviceContent = clear_file_domain($params['service_content'] ?? '');
        $privacyContent = clear_file_domain($params['privacy_content'] ?? '');
        ConfigService::set('agreement', 'service_title', $params['service_title'] ?? '');
        ConfigService::set('agreement', 'service_content', $serviceContent);
        ConfigService::set('agreement', 'privacy_title', $params['privacy_title'] ?? '');
        ConfigService::set('agreement', 'privacy_content', $privacyContent);
    }


    /**
     * 获取政策协议
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getAgreement() : array
    {
        $config = [
            'service_title' => ConfigService::get('agreement', 'service_title'),
            'service_content' => ConfigService::get('agreement', 'service_content'),
            'privacy_title' => ConfigService::get('agreement', 'privacy_title'),
            'privacy_content' => ConfigService::get('agreement', 'privacy_content'),
        ];

        $config['service_content'] = get_file_domain($config['service_content'] ?? '');
        $config['privacy_content'] = get_file_domain($config['privacy_content'] ?? '');

        return $config;
    }

    /**
     * 获取站点统计配置
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getSiteStatistics(): array
    {
        return [
            'clarity_code' => ConfigService::get('siteStatistics', 'clarity_code')
        ];
    }

    /**
     * 设置站点统计配置
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function setSiteStatistics(array $params): void
    {
        ConfigService::set('siteStatistics', 'clarity_code', $params['clarity_code']);
    }
}