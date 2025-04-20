<?php
declare(strict_types=1);

namespace app\common\service\pay;


use app\common\enum\PayEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\logic\PayNotifyLogic;
use app\common\model\recharge\RechargeOrder;
use app\common\model\user\UserAuth;
use app\common\service\wechat\WeChatConfigService;
use EasyWeChat\Kernel\Exceptions\BadResponseException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Pay\Application;
use EasyWeChat\Pay\Message;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


/**
 * 微信支付
 * @class WeChatPayService
 * @package app\common\service\pay
 * @author LZH
 * @date 2025/2/19
 */
class WeChatPayService extends BasePayService
{
    /**
     * 授权信息
     * @var UserAuth|array|\think\Model
     */
    protected mixed $auth;


    /**
     * 微信配置
     * @var
     */
    protected array $config;


    /**
     * easyWeChat实例
     * @var
     */
    protected Application $app;


    /**
     * 当前使用客户端
     * @var
     */
    protected mixed $terminal;


    /**
     * 初始化微信支付配置
     * @param $terminal //用户终端
     * @param string|null $userId //用户id(获取授权openid)
     * @throws InvalidArgumentException
     */
    public function __construct(mixed $terminal, string $userId = null)
    {
        $this->terminal = $terminal;
        $this->config = WeChatConfigService::getPayConfigByTerminal($terminal);
        $this->app = new Application($this->config);
        if ($userId !== null) {
            $this->auth = UserAuth::where(['user_id' => $userId, 'terminal' => $terminal])->findOrEmpty();
        }
    }


    /**
     * 发起微信支付统一下单
     * @param object $from
     * @param array $order
     * @return array|false
     * @author LZH
     * @date 2025/2/19
     */
    public function pay(object $from, array $order): bool|array
    {
        try {
            switch ($this->terminal) {
                case UserTerminalEnum::WECHAT_MMP:
                    $config = WeChatConfigService::getMnpConfig();
                    $result = $this->jsapiPay($from, $order, $config['app_id']);
                    break;
                case UserTerminalEnum::WECHAT_OA:
                    $config = WeChatConfigService::getOaConfig();
                    $result = $this->jsapiPay($from, $order, $config['app_id']);
                    break;
                case UserTerminalEnum::IOS:
                case UserTerminalEnum::ANDROID:
                    $config = WeChatConfigService::getOpConfig();
                    $result = $this->appPay($from, $order, $config['app_id']);
                    break;
                case UserTerminalEnum::H5:
                    $config = WeChatConfigService::getOaConfig();
                    $result = $this->mwebPay($from, $order, $config['app_id']);
                    break;
                case UserTerminalEnum::PC:
                    $config = WeChatConfigService::getOaConfig();
                    $result = $this->nativePay($from, $order, $config['app_id']);
                    break;
                default:
                    throw new \Exception('支付方式错误');
            }

            return [
                'config' => $result,
                'pay_way' => PayEnum::WECHAT_PAY
            ];
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }


    /**
     * jsapiPay
     * @param object $from
     * @param array $order
     * @param string $appId
     * @return array
     * @throws InvalidArgumentException
     * @throws BadResponseException
     * @throws InvalidConfigException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public function jsapiPay(object $from, array $order, string $appId): array
    {
        $response = $this->app->getClient()->postJson("v3/pay/transactions/jsapi", [
            "appid" => $appId,
            "mchid" => $this->config['mch_id'],
            "description" => $this->payDesc($from),
            "out_trade_no" => $order['pay_sn'],
            "notify_url" => $this->config['notify_url'],
            "amount" => [
                "total" => intval($order['order_amount'] * 100),
            ],
            "payer" => [
                "openid" => $this->auth['openid']
            ],
            'attach' => $from
        ]);

        $result = $response->toArray(false);
        $this->checkResultFail($result);
        return $this->getPrepayConfig($result['prepay_id'], $appId);
    }


    /**
     * 网站native
     * @param $from
     * @param $order
     * @param $appId
     * @return mixed
     * @throws \Exception
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     * @author LZH
     * @date 2025/2/19
     */
    public function nativePay(object $from, array $order, string $appId): mixed
    {
        $response = $this->app->getClient()->postJson('v3/pay/transactions/native', [
            'appid' => $appId,
            'mchid' => $this->config['mch_id'],
            'description' => $this->payDesc($from),
            'out_trade_no' => $order['pay_sn'],
            'notify_url' => $this->config['notify_url'],
            'amount' => [
                'total' => intval($order['order_amount'] * 100),
            ],
            'attach' => $from
        ]);
        $result = $response->toArray(false);
        $this->checkResultFail($result);
        return $result['code_url'];
    }


    /**
     * appPay
     * @param object $from
     * @param array $order
     * @param string $appId
     * @return mixed
     * @throws BadResponseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public function appPay(object $from, array $order, string $appId): mixed
    {
        $response = $this->app->getClient()->postJson('v3/pay/transactions/app', [
            'appid' => $appId,
            'mchid' => $this->config['mch_id'],
            'description' => $this->payDesc($from),
            'out_trade_no' => $order['pay_sn'],
            'notify_url' => $this->config['notify_url'],
            'amount' => [
                'total' => intval($order['order_amount'] * 100),
            ],
            'attach' => $from
        ]);
        $result = $response->toArray(false);
        $this->checkResultFail($result);
        return $result['prepay_id'];
    }


    /**
     * h5
     * @param object $from
     * @param array $order
     * @param string $appId
     * @return string
     * @throws BadResponseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public function mwebPay(object $from, array $order, string $appId): string
    {
        $ip = request()->ip();
        if (!empty(env('project.test_web_ip')) && env('APP_DEBUG')) {
            $ip = env('project.test_web_ip');
        }

        $response = $this->app->getClient()->postJson('v3/pay/transactions/h5', [
            'appid' => $appId,
            'mchid' => $this->config['mch_id'],
            'description' => $this->payDesc($from),
            'out_trade_no' => $order['pay_sn'],
            'notify_url' => $this->config['notify_url'],
            'amount' => [
                'total' => intval(strval($order['order_amount'] * 100)),
            ],
            'attach' => $from,
            'scene_info' => [
                'payer_client_ip' => $ip,
                'h5_info' => [
                    'type' => 'Wap',
                ]
            ]
        ]);
        $result = $response->toArray(false);
        $this->checkResultFail($result);

        $domain = request()->domain();
        if (!empty(env('project.test_web_domain')) && env('APP_DEBUG')) {
            $domain = env('project.test_web_domain');
        }
        $redirectUrl = $domain . '/mobile'. $order['redirect_url'] .'?id=' . $order['id'] . '&from='. $from . '&checkPay=true';
        return $result['h5_url'] . '&redirect_url=' . urlencode($redirectUrl);
    }


    /**
     * 退款
     * @param array $refundData
     * @return mixed
     * @throws \Exception
     * @throws TransportExceptionInterface|DecodingExceptionInterface
     * @author LZH
     * @date 2025/2/19
     */
    public function refund(array $refundData): mixed
    {
        $response =  $this->app->getClient()->postJson('v3/refund/domestic/refunds', [
            'transaction_id' => $refundData['transaction_id'],
            'out_refund_no' => $refundData['refund_sn'],
            'amount' => [
                'refund' => intval($refundData['refund_amount'] * 100),
                'total' => intval($refundData['total_amount'] * 100),
                'currency' => 'CNY',
            ]
        ]);
        $result = $response->toArray(false);
        $this->checkResultFail($result);
        return $result;
    }


    /**
     * 查询退款
     * @param string $refundSn
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @author LZH
     * @date 2025/2/19
     */
    public function queryRefund(string $refundSn): array
    {
        $response = $this->app->getClient()->get("v3/refund/domestic/refunds/{$refundSn}");
        return $response->toArray(false);
    }


    /**
     * 支付描述
     * @param string $from
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function payDesc(string $from): string
    {
        $desc = [
            'order' => '商品',
            'recharge' => '充值',
        ];
        return $desc[$from] ?? '商品';
    }


    /**
     * 捕获错误
     * @param array $result
     * @return void
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public function checkResultFail(array $result): void
    {
        if (!empty($result['code']) || !empty($result['message'])) {
            throw new \Exception('微信:'. $result['code'] . '-' . $result['message']);
        }
    }

    /**
     * 预支付配置
     * @param string $prepayId
     * @param string $appId
     * @return array
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public function getPrepayConfig(string $prepayId, string $appId): array
    {
        return $this->app->getUtils()->buildBridgeConfig($prepayId, $appId);
    }


    /**
     * 支付回调
     * @return mixed
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @author LZH
     * @date 2025/2/19
     */
    public function notify(): mixed
    {
        $server = $this->app->getServer();
        // 支付通知
        $server->handlePaid(function (Message $message) {
            if ($message['trade_state'] === 'SUCCESS') {
                $extra['transaction_id'] = $message['transaction_id'];
                $attach = $message['attach'];
                $message['out_trade_no'] = mb_substr($message['out_trade_no'], 0, 18);
                switch ($attach) {
                    case 'recharge':
                        $order = RechargeOrder::where(['sn' => $message['out_trade_no']])->findOrEmpty();
                        if($order->isEmpty() || $order->pay_status == PayEnum::ISPAID) {
                            return true;
                        }
                        PayNotifyLogic::handle('recharge', $message['out_trade_no'], $extra);
                        break;
                }
            }
            return true;
        });

        // 退款通知
        $server->handleRefunded(function (Message $message) {
            return true;
        });
        return $server->serve();
    }

}