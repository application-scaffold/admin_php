<?php

namespace app\front_api\logic;

use app\common\logic\BaseLogic;
use app\common\service\wechat\WeChatOaService;
use EasyWeChat\Kernel\Exceptions\Exception;

/**
 * 微信
 * @class WechatLogic
 * @package app\front_api\logic
 * @author LZH
 * @date 2025/2/20
 */
class WechatLogic extends BaseLogic
{

    /**
     * 微信JSSDK授权接口
     * @param $params
     * @return false|mixed
     * @author LZH
     * @date 2025/2/20
     */
    public static function jsConfig($params)
    {
        try {
            $url = urldecode($params['url']);
            return (new WeChatOaService())->getJsConfig($url, [
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareWeibo',
                'onMenuShareQZone',
                'openLocation',
                'getLocation',
                'chooseWXPay',
                'updateAppMessageShareData',
                'updateTimelineShareData',
                'openAddress',
                'scanQRCode'
            ]);
        } catch (Exception $e) {
            self::setError('获取jssdk失败:' . $e->getMessage());
            return false;
        }
    }
}