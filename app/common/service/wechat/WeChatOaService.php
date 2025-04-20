<?php
declare(strict_types=1);

namespace app\common\service\wechat;

use EasyWeChat\Kernel\Contracts\Server;
use EasyWeChat\Kernel\Exceptions\Exception;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\HttpClient\Response;
use EasyWeChat\OfficialAccount\Application;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;


/**
 * 公众号相关
 * @class WeChatOaService
 * @package app\common\service\wechat
 * @author LZH
 * @date 2025/2/19
 */
class WeChatOaService
{

    protected Application $app;

    protected array $config;

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct()
    {
        $this->config = $this->getConfig();
        $this->app = new Application($this->config);
    }


    /**
     * easywechat服务端
     * @return Server|\EasyWeChat\OfficialAccount\Server
     * @throws InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     * @author LZH
     * @date 2025/2/19
     */
    public function getServer(): \EasyWeChat\OfficialAccount\Server|Server
    {
        return $this->app->getServer();
    }


    /**
     * 配置
     * @return array
     * @throws Exception
     * @author LZH
     * @date 2025/2/19
     */
    protected function getConfig(): array
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
     * @return array
     * @throws Exception
     * @throws InvalidArgumentException
     * @author LZH
     * @date 2025/2/19
     */
    public function getOaResByCode(string $code): array
    {
        $response = $this->app->getOAuth()
            ->scopes(['snsapi_userinfo'])
            ->userFromCode($code)
            ->getRaw();

        if (empty($response['openid'])) {
            throw new Exception('获取openID失败');
        }

        return $response;
    }


    /**
     * 公众号跳转url
     * @param string $url
     * @return string
     * @throws InvalidArgumentException
     * @author LZH
     * @date 2025/2/19
     */
    public function getCodeUrl(string $url): string
    {
        return $this->app->getOAuth()
            ->scopes(['snsapi_userinfo'])
            ->redirect($url);
    }

    /**
     * 创建公众号菜单
     * @param array $buttons
     * @param array $matchRule
     * @return ResponseInterface|Response
     * @throws TransportExceptionInterface
     * @author LZH
     * @date 2025/2/19
     */
    public function createMenu(array $buttons, array $matchRule = []): ResponseInterface|Response
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
     * @param string $url
     * @param array $jsApiList
     * @param array $openTagList
     * @param bool $debug
     * @return array
     * @throws TransportExceptionInterface
     * @throws HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @author LZH
     * @date 2025/2/19
     */
    public function getJsConfig(string $url, array $jsApiList, array $openTagList = [], bool $debug = false): array
    {
        return $this->app->getUtils()->buildJsSdkConfig($url, $jsApiList, $openTagList, $debug);
    }

}