<?php

declare (strict_types=1);

namespace app\admin_api\http\middleware;

use think\Request;

/**
 * 演示环境数据加密
 * @class EncryDemoDataMiddleware
 * @package app\admin_api\http\middleware
 * @author LZH
 * @date 2025/2/19
 */
class EncryDemoDataMiddleware
{

    // 需要过滤的接口
    protected array $needCheck = [
        // 存储配置
        'setting.storage/detail',
        // 短信配置
        'notice.smsConfig/detail',
        // 公众号配置
        'channel.official_account_setting/getConfig',
        // 小程序配置
        'channel.mnp_settings/getConfig',
        // 开放平台配置
        'channel.open_setting/getConfig',
        // 支付配置
        'setting.pay.pay_config/getConfig',
    ];

    // 可以排除字段
    protected array $excludeParams = [
        'name',
        'icon',
        'image',
        'qr_code',
        'interface_version',
        'merchant_type',
    ];


    public function handle(Request $request, \Closure $next)
    {
        $response = $next($request);

        // 非需校验的接口 或者 未开启演示模式
        $accessUri = strtolower($request->controller() . '/' . $request->action());
        if (!in_array($accessUri, lower_uri($this->needCheck)) || !env('project.demo_env')) {
            return $response;
        }

        // 非json数据
        if (!method_exists($response, 'header') || !in_array('application/json; charset=utf-8', $response->getHeader())) {
            return $response;
        }

        $data = $response->getData();
        if (!is_array($data) || empty($data)) {
            return $response;
        }

        foreach ($data['data'] as $key => $item) {
            // 字符串
            if (is_string($item)) {
                $data['data'][$key] = $this->getEncryData($key, $item);
                continue;
            }
            // 数组
            if (is_array($item)) {
                foreach ($item as $itemKey => $itemValue) {
                    $data['data'][$key][$itemKey] = $this->getEncryData($itemKey, $itemValue);
                }
            }
        }

        return $response->data($data);
    }


    /**
     * 加密配置
     * @param string $key
     * @param mixed $value
     * @return mixed|string
     * @author LZH
     * @date 2025/2/19
     */
    protected function getEncryData(string $key, mixed $value): mixed
    {
        // 非隐藏字段
        if (in_array($key, $this->excludeParams)) {
            return $value;
        }

        // 隐藏字段
        if (is_string($value)) {
            return '******';
        }
        return $value;
    }

}