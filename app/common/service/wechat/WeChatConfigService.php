<?php
declare(strict_types=1);

namespace app\common\service\wechat;

use app\common\enum\PayEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\model\pay\PayConfig;
use app\common\service\ConfigService;

/**
 * 微信配置类
 * @class WeChatConfigService
 * @package app\common\service\wechat
 * @author LZH
 * @date 2025/2/19
 */
class WeChatConfigService
{

    /**
     * 获取小程序配置
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getMnpConfig(): array
    {
        return [
            'app_id' => ConfigService::get('mnp_setting', 'app_id'),
            'secret' => ConfigService::get('mnp_setting', 'app_secret'),
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => app()->getRootPath() . 'runtime/wechat/' . date('Ym') . '/' . date('d') . '.log'
            ],
        ];
    }


    /**
     * 获取微信公众号配置
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getOaConfig(): array
    {
        return [
            'app_id' => ConfigService::get('oa_setting', 'app_id'),
            'secret' => ConfigService::get('oa_setting', 'app_secret'),
            'token' => ConfigService::get('oa_setting', 'token'),
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => app()->getRootPath() . 'runtime/wechat/' . date('Ym') . '/' . date('d') . '.log'
            ],
        ];
    }


    /**
     * 获取微信开放平台配置
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getOpConfig(): array
    {
        return [
            'app_id' => ConfigService::get('open_platform', 'app_id'),
            'secret' => ConfigService::get('open_platform', 'app_secret'),
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => app()->getRootPath() . 'runtime/wechat/' . date('Ym') . '/' . date('d') . '.log'
            ],
        ];
    }


    /**
     * 根据终端获取支付配置
     * @param int $terminal
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getPayConfigByTerminal(int $terminal): array
    {
        switch ($terminal) {
            case UserTerminalEnum::WECHAT_MMP:
                $notifyUrl = (string)url('pay/notifyMnp', [], false, true);
                break;
            case UserTerminalEnum::WECHAT_OA:
            case UserTerminalEnum::PC:
            case UserTerminalEnum::H5:
                $notifyUrl = (string)url('pay/notifyOa', [], false, true);
                break;
            case UserTerminalEnum::ANDROID:
            case UserTerminalEnum::IOS:
                $notifyUrl = (string)url('pay/notifyApp', [], false, true);
                break;
        }

        $pay = PayConfig::where(['pay_way' => PayEnum::WECHAT_PAY])->findOrEmpty()->toArray();
        //判断是否已经存在证书文件夹，不存在则新建
        if (!file_exists(app()->getRootPath() . 'runtime/cert')) {
            mkdir(app()->getRootPath() . 'runtime/cert', 0775, true);
        }
        //写入文件
        $apiclientCert = $pay['config']['apiclient_cert'] ?? '';
        $apiclientKey = $pay['config']['apiclient_key'] ?? '';

        $certPath = app()->getRootPath() . 'runtime/cert/' . md5($apiclientCert) . '.pem';
        $keyPath = app()->getRootPath() . 'runtime/cert/' . md5($apiclientKey) . '.pem';

        if (!empty($apiclientCert) && !file_exists($certPath)) {
            static::setCert($certPath, trim($apiclientCert));
        }
        if (!empty($apiclientKey) && !file_exists($keyPath)) {
            static::setCert($keyPath, trim($apiclientKey));
        }

        return [
            // 商户号
            'mch_id' => $pay['config']['mch_id'] ?? '',
            // 商户证书
            'private_key' => $keyPath,
            'certificate' => $certPath,
            // v3 API 秘钥
            'secret_key' => $pay['config']['pay_sign_key'] ?? '',
            'notify_url' => $notifyUrl,
            'http' => [
                'throw'  => true, // 状态码非 200、300 时是否抛出异常，默认为开启
                'timeout' => 5.0,
            ]
        ];
    }


    /**
     * 临时写入证书
     * @param string $path
     * @param string $cert
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function setCert(string $path, string $cert): void
    {
        $fopenPath = fopen($path, 'w');
        fwrite($fopenPath, $cert);
        fclose($fopenPath);
    }

}