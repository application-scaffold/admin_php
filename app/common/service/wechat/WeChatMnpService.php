<?php
declare(strict_types=1);

namespace app\common\service\wechat;


use EasyWeChat\Kernel\Exceptions\Exception;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\HttpClient\Response;
use EasyWeChat\MiniApp\Application;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;


/**
 * 微信功能类
 * @class WeChatMnpService
 * @package app\common\service\wechat
 * @author LZH
 * @date 2025/2/19
 */
class WeChatMnpService
{

    protected Application $app;

    protected array $config;

    /**
     * @throws InvalidArgumentException
     * @throws \Exception
     */
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
    protected function getConfig(): array
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
     * @return array
     * @throws Exception
     * @throws TransportExceptionInterface
     * @throws HttpException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @author LZH
     * @date 2025/2/19
     */
    public function getMnpResByCode(string $code): array
    {
        $utils = $this->app->getUtils();
        $response = $utils->codeToSession($code);

        if (empty($response['openid'])) {
            throw new Exception('获取openID失败');
        }

        return $response;
    }


    /**
     * 获取手机号
     * @param string $code
     * @return ResponseInterface|Response
     * @throws TransportExceptionInterface
     * @author LZH
     * @date 2025/2/19
     */
    public function getUserPhoneNumber(string $code): ResponseInterface|Response
    {
        return $this->app->getClient()->postJson('wxa/business/getuserphonenumber', [
            'code' => $code,
        ]);
    }

}