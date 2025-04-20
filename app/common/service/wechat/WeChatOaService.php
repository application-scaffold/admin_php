<?php

namespace app\common\service\wechat;

use EasyWeChat\Kernel\Exceptions\Exception;
use EasyWeChat\OfficialAccount\Application;


/**
 * 公众号相关
 * @class WeChatOaService
 * @package app\common\service\wechat
 * @author LZH
 * @date 2025/2/19
 */
class WeChatOaService
{

    protected $app;

    protected $config;

    public function __construct()
    {
        $this->config = $this->getConfig();
        $this->app = new Application($this->config);
    }


    /**
     * easywechat服务端
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function getServer()
    {
        return $this->app->getServer();
    }


    /**
     * 配置
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    protected function getConfig()
    {
        $config = WeChatConfigService::getOaConfig();
        if (empty($config['app_id']) || empty($config['secret'])) {
            throw new Exception('请先设置公众号配置');
        }
        return $config;
    }


    /**
     * 公众号-根据code获取微信信息
     * @param string $code
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function getOaResByCode(string $code)
    {
        $response = $this->app->getOAuth()
            ->scopes(['snsapi_userinfo'])
            ->userFromCode($code)
            ->getRaw();

        if (!isset($response['openid']) || empty($response['openid'])) {
            throw new Exception('获取openID失败');
        }

        return $response;
    }


    /**
     * 公众号跳转url
     * @param string $url
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function getCodeUrl(string $url)
    {
        return $this->app->getOAuth()
            ->scopes(['snsapi_userinfo'])
            ->redirect($url);
    }

    /**
     * 创建公众号菜单
     * @param array $buttons
     * @param array $matchRule
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function createMenu(array $buttons, array $matchRule = [])
    {
        if (!empty($matchRule)) {
            return $this->app->getClient()->postJson('cgi-bin/menu/addconditional', [
                'button' => $buttons,
                'matchrule' => $matchRule,
            ]);
        }

        return $this->app->getClient()->postJson('cgi-bin/menu/create', ['button' => $buttons]);
    }


    /**
     * 获取jssdkConfig
     * @param $url
     * @param $jsApiList
     * @param $openTagList
     * @param $debug
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function getJsConfig($url, $jsApiList, $openTagList = [], $debug = false)
    {
        return $this->app->getUtils()->buildJsSdkConfig($url, $jsApiList, $openTagList, $debug);
    }

}