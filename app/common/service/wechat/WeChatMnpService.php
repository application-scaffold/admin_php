<?php

namespace app\common\service\wechat;


use EasyWeChat\Kernel\Exceptions\Exception;
use EasyWeChat\MiniApp\Application;


/**
 * 微信功能类
 * @class WeChatMnpService
 * @package app\common\service\wechat
 * @author LZH
 * @date 2025/2/19
 */
class WeChatMnpService
{

    protected $app;

    protected $config;

    public function __construct()
    {
        $this->config = $this->getConfig();
        $this->app = new Application($this->config);
    }


    /**
     * 配置
     * @return array
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    protected function getConfig()
    {
        $config = WeChatConfigService::getMnpConfig();
        if (empty($config['app_id']) || empty($config['secret'])) {
            throw new \Exception('请先设置小程序配置');
        }
        return $config;
    }


    /**
     * 程序-根据code获取微信信息
     * @param string $code
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function getMnpResByCode(string $code)
    {
        $utils = $this->app->getUtils();
        $response = $utils->codeToSession($code);

        if (!isset($response['openid']) || empty($response['openid'])) {
            throw new Exception('获取openID失败');
        }

        return $response;
    }


    /**
     * 获取手机号
     * @param string $code
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function getUserPhoneNumber(string $code)
    {
        return $this->app->getClient()->postJson('wxa/business/getuserphonenumber', [
            'code' => $code,
        ]);
    }

}