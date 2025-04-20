<?php


namespace app\admin_api\logic\setting;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;
use app\common\service\FileService;

/**
 * 客服设置逻辑
 * @class CustomerServiceLogic
 * @package app\admin_api\logic\setting
 * @author LZH
 * @date 2025/2/19
 */
class CustomerServiceLogic extends BaseLogic
{

    /**
     * 获取客服设置
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getConfig()
    {
        $qrCode = ConfigService::get('customer_service', 'qr_code');
        $qrCode = empty($qrCode) ? '' : FileService::getFileUrl($qrCode);
        $config = [
            'qr_code' => $qrCode,
            'wechat' => ConfigService::get('customer_service', 'wechat', ''),
            'phone' => ConfigService::get('customer_service', 'phone', ''),
            'service_time' => ConfigService::get('customer_service', 'service_time', ''),
        ];
        return $config;
    }

    /**
     * 设置客服设置
     * @param $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function setConfig($params)
    {
        $allowField = ['qr_code','wechat','phone','service_time'];
        foreach($params as $key => $value) {
            if(in_array($key, $allowField)) {
                if ($key == 'qr_code') {
                    $value = FileService::setFileUrl($value);
                }
                ConfigService::set('customer_service', $key, $value);
            }
        }
    }
}