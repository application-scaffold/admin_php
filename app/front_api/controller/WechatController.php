<?php
declare(strict_types=1);

namespace app\front_api\controller;

use app\front_api\logic\WechatLogic;
use app\front_api\validate\WechatValidate;
use think\response\Json;


/**
 * 微信
 * @class WechatController
 * @package app\front_api\controller
 * @author LZH
 * @date 2025/2/19
 */
class WechatController extends BaseApiController
{
    public array $notNeedLogin = ['jsConfig'];

    /**
     * 微信JSSDK授权接口
     * @return Json
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @author LZH
     * @date 2025/2/19
     */
    public function jsConfig(): Json
    {
        $params = (new WechatValidate())->goCheck('jsConfig');
        $result = WechatLogic::jsConfig($params);
        if ($result === false) {
            return $this->fail(WechatLogic::getError(), [], 0, 0);
        }
        return $this->data($result);
    }
}