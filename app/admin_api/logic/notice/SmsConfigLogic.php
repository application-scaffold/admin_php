<?php
declare(strict_types=1);

namespace app\admin_api\logic\notice;

use app\common\enum\notice\SmsEnum;
use app\common\logic\BaseLogic;
use app\common\service\ConfigService;

/**
 * 短信配置逻辑层
 * @class SmsConfigLogic
 * @package app\admin_api\logic\notice
 * @author LZH
 * @date 2025/2/19
 */
class SmsConfigLogic extends BaseLogic
{
    /**
     * 获取短信配置
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getConfig(): array
    {
        $config = [
            ConfigService::get('sms', 'ali', ['type' => 'ali', 'name' => '阿里云短信', 'status' => 1]),
            ConfigService::get('sms', 'tencent', ['type' => 'tencent', 'name' => '腾讯云短信', 'status' => 0]),
        ];
        return $config;
    }


    /**
     * 短信配置
     * @param array $params
     * @return true|void
     * @author LZH
     * @date 2025/2/19
     */
    public static function setConfig(array $params)
    {
        $type = $params['type'];
        $params['name'] = self::getNameDesc(strtoupper($type));
        ConfigService::set('sms', $type, $params);
        $default = ConfigService::get('sms', 'engine', false);
        if ($params['status'] == 1 && $default === false) {
            // 启用当前短信配置 并 设置当前短信配置为默认
            ConfigService::set('sms', 'engine', strtoupper($type));
            return true;
        }
        if ($params['status'] == 1 && $default != strtoupper($type)) {
            // 找到默认短信配置
            $defaultConfig = ConfigService::get('sms', strtolower($default));
            // 状态置为禁用 并 更新
            $defaultConfig['status'] = 0;
            ConfigService::set('sms', strtolower($default), $defaultConfig);
            // 设置当前短信配置为默认
            ConfigService::set('sms', 'engine', strtoupper($type));
            return true;
        }
    }

    /**
     * 查看短信配置详情
     * @param array $params
     * @return array|int|mixed|string
     * @author LZH
     * @date 2025/2/19
     */
    public static function detail(array $params): mixed
    {
        $default = [];
        switch ($params['type']) {
            case 'ali':
                $default = [
                    'sign' => '',
                    'app_key' => '',
                    'secret_key' => '',
                    'status' => 1,
                    'name' => '阿里云短信',
                ];
                break;
            case 'tencent':
                $default = [
                    'sign' => '',
                    'app_id' => '',
                    'secret_key' => '',
                    'status' => 0,
                    'secret_id' => '',
                    'name' => '腾讯云短信',
                ];
                break;
        }
        $result = ConfigService::get('sms', $params['type'], $default);
        $result['status'] = intval($result['status'] ?? 0);
        return $result;
    }

    /**
     * 获取短信平台名称
     * @param string $value
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public static function getNameDesc(string $value): string
    {
        $desc = [
            'ALI' => '阿里云短信',
            'TENCENT' => '腾讯云短信',
        ];
        return $desc[$value] ?? '';
    }
}