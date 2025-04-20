<?php
declare(strict_types=1);

namespace app\common\service\wechat;

use app\common\logic\BaseLogic;
use WpOrg\Requests\Requests;

/**
 * 自定义微信请求
 * @class WeChatRequestService
 * @package app\common\service\wechat
 * @author LZH
 * @date 2025/2/19
 */
class WeChatRequestService extends BaseLogic
{

    /**
     * 获取网站扫码登录地址
     * @param string $appId
     * @param string $redirectUri
     * @param string $state
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public static function getScanCodeUrl(string $appId, string $redirectUri, string $state): string
    {
        $url = 'https://open.weixin.qq.com/connect/qrconnect?';
        $url .= 'appid=' . $appId . '&redirect_uri=' . $redirectUri . '&response_type=code&scope=snsapi_login';
        $url .= '&state=' . $state . '#wechat_redirect';
        return $url;
    }

    /**
     * 通过code获取用户信息(access_token,openid,unionid等)
     * @param string $code
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public static function getUserAuthByCode(string $code): mixed
    {
        $config = WeChatConfigService::getOpConfig();
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token';
        $url .= '?appid=' . $config['app_id'] . '&secret=' . $config['secret'] . '&code=' . $code;
        $url .= '&grant_type=authorization_code';
        $requests = Requests::get($url);
        return json_decode($requests->body, true);
    }


    /**
     * 通过授权信息获取用户信息
     * @param string $accessToken
     * @param string $openId
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public static function getUserInfoByAuth(string $accessToken, string $openId): mixed
    {
        $url = 'https://api.weixin.qq.com/sns/userinfo';
        $url .= '?access_token=' . $accessToken . '&openid=' . $openId;
        $response = Requests::get($url);
        return json_decode($response->body, true);
    }

}