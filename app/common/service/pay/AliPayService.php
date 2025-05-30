<?php
declare(strict_types=1);

namespace app\common\service\pay;

use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Kernel\Config;
use Alipay\EasySDK\Payment\Common\Models\AlipayTradeFastpayRefundQueryResponse;
use Alipay\EasySDK\Payment\Common\Models\AlipayTradeQueryResponse;
use Alipay\EasySDK\Payment\Common\Models\AlipayTradeRefundResponse;
use app\common\enum\PayEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\logic\PayNotifyLogic;
use app\common\model\pay\PayConfig;
use app\common\model\recharge\RechargeOrder;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Log;
use Alipay\EasySDK\Kernel\Payment;

/**
 * 支付宝支付
 * @class AliPayService
 * @package app\common\service\pay
 * @author LZH
 * @date 2025/2/19
 */
class AliPayService extends BasePayService
{

    /**
     * 用户客户端
     * @var
     */
    protected mixed $terminal;

    /**
     * 支付实例
     * @var
     */
    protected Payment $pay;

    /**
     * 初始化设置
     * AliPayService constructor.
     * @throws \Exception
     */
    public function __construct(mixed $terminal = null)
    {
        //设置用户终端
        $this->terminal = $terminal;
        //初始化支付配置
        Factory::setOptions($this->getOptions());
        $this->pay = Factory::payment();
    }


    /**
     * 支付设置
     * @return Config
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function getOptions(): Config
    {
        $config = (new PayConfig())->where(['pay_way' => PayEnum::ALI_PAY])->find();
        if (empty($config)) {
            throw new \Exception('请配置好支付设置');
        }
        $options = new Config();
        $options->protocol = 'https';
        $options->gatewayHost = 'openapi.alipay.com';
//        $options->gatewayHost = 'openapi.alipaydev.com'; //测试沙箱地址
        $options->signType = 'RSA2';
        $options->appId = $config['config']['app_id'] ?? '';
        // 应用私钥
        $options->merchantPrivateKey = $config['config']['private_key'] ?? '';
        //接口加签方式
        // 秘钥模式
        if ($config['config']['mode'] == 'normal_mode') {
            //支付宝公钥
            $options->alipayPublicKey = $config['config']['ali_public_key'] ?? '';
        }
        
        //证书模式
        if ($config['config']['mode'] == 'certificate') {
            //判断是否已经存在证书文件夹，不存在则新建
            if (!file_exists(app()->getRootPath() . 'runtime/certificate')) {
                mkdir(app()->getRootPath() . 'runtime/certificate', 0775, true);
            }
            //写入文件
            $publicCert = $config['config']['public_cert'] ?? '';
            $aliPublicCert = $config['config']['ali_public_cert'] ?? '';
            $aliRootCert = $config['config']['ali_root_cert'] ?? '';
            $publicCertPath = app()->getRootPath() . 'runtime/certificate/' . md5($publicCert) . '.crt';
            $aliPublicCertPath = app()->getRootPath() . 'runtime/certificate/' . md5($aliPublicCert) . '.crt';
            $aliRootCertPath = app()->getRootPath() . 'runtime/certificate/' . md5($aliRootCert) . '.crt';
            if (!file_exists($publicCertPath)) {
                $fopenPublicCertPath = fopen($publicCertPath, 'w');
                fwrite($fopenPublicCertPath, $publicCert);
                fclose($fopenPublicCertPath);
            }
            if (!file_exists($aliPublicCertPath)) {
                $fopenAliPublicCertPath = fopen($aliPublicCertPath, 'w');
                fwrite($fopenAliPublicCertPath, $aliPublicCert);
                fclose($fopenAliPublicCertPath);
            }
            if (!file_exists($aliRootCertPath)) {
                $fopenAliRootCertPath = fopen($aliRootCertPath, 'w');
                fwrite($fopenAliRootCertPath, $aliRootCert);
                fclose($fopenAliRootCertPath);
            }
            //应用公钥证书路径
            $options->merchantCertPath = $publicCertPath;
            //支付宝公钥证书路径
            $options->alipayCertPath = $aliPublicCertPath;
            //支付宝根证书路径
            $options->alipayRootCertPath = $aliRootCertPath;
        }
        //回调地址
        $options->notifyUrl = (string)url('pay/aliNotify', [], false, true);
        return $options;
    }


    /**
     * 支付
     * @param string $from //订单来源;order-商品订单;recharge-充值订单
     * @param array $order //订单信息
     * @return array|bool
     * @author LZH
     * @date 2025/2/19
     */
    public function pay(object $from, array $order): array|bool
    {
        try {
            $result = match ($this->terminal) {
                UserTerminalEnum::PC => $this->pagePay($from, $order),
                UserTerminalEnum::IOS, UserTerminalEnum::ANDROID => $this->appPay($from, $order),
                UserTerminalEnum::WECHAT_OA, UserTerminalEnum::H5 => $this->wapPay($from, $order),
                default => throw new \Exception('支付方式错误'),
            };
            return [
                'config' => $result,
                'pay_way' => PayEnum::ALI_PAY
            ];
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 支付回调
     * @param array $data
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public function notify(array $data): bool
    {
        try {
            $verify = $this->pay->common()->verifyNotify($data);
            if (false === $verify) {
                throw new \Exception('异步通知验签失败');
            }
            if (!in_array($data['trade_status'], ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
                return true;
            }
            $extra['transaction_id'] = $data['trade_no'];
            //验证订单是否已支付
            switch ($data['passback_params']) {
                case 'recharge':
                    $order = RechargeOrder::where(['sn' => $data['out_trade_no']])->findOrEmpty();
                    if ($order->isEmpty() || $order->pay_status == PayEnum::ISPAID) {
                        return true;
                    }
                    PayNotifyLogic::handle('recharge', $data['out_trade_no'], $extra);
                    break;
            }

            return true;
        } catch (\Exception $e) {
            $record = [
                __CLASS__,
                __FUNCTION__,
                $e->getFile(),
                $e->getLine(),
                $e->getMessage()
            ];
            Log::write(implode('-', $record));
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * PC支付
     * @param object $attach //附加参数(在回调时会返回)
     * @param array $order //订单信息
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function pagePay(object $attach, array $order): string
    {
        $domain = request()->domain();
        $result = $this->pay->page()->optional('passback_params', $attach)->pay(
            '订单:' . $order['sn'],
            $order['sn'],
            $order['order_amount'],
            $domain . $order['redirect_url']
        );
        return $result->body;
    }

    /**
     * APP支付
     * @param object $attach //附加参数(在回调时会返回)
     * @param array $order //订单信息
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function appPay(object $attach, array $order): string
    {
        $result = $this->pay->app()->optional('passback_params', $attach)->pay(
            $order['sn'],
            $order['sn'],
            $order['order_amount']
        );
        return $result->body;
    }

    /**
     * 手机网页支付
     * @param object $attach //附加参数(在回调时会返回)
     * @param array $order //订单信息
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function wapPay(object $attach,array $order): string
    {
        $domain = request()->domain();
        $url = $domain . '/mobile' . $order['redirect_url'] .'?id=' . $order['id'] . '&from='. $attach . '&checkPay=true';;
        $result = $this->pay->wap()->optional('passback_params', $attach)->pay(
            '订单:' . $order['sn'],
            $order['sn'],
            $order['order_amount'],
            $url,
            $url
        );
        return $result->body;
    }

    /**
     * 查询订单
     * @param string $orderSn
     * @return AlipayTradeQueryResponse
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public function checkPay(string $orderSn): AlipayTradeQueryResponse
    {
        return $this->pay->common()->query($orderSn);
    }

    /**
     * 退款
     * @param string $orderSn
     * @param int $orderAmount
     * @param object $outRequestNo
     * @return AlipayTradeRefundResponse
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public function refund(string $orderSn, int $orderAmount, object $outRequestNo): AlipayTradeRefundResponse
    {
        return $this->pay->common()->optional('out_request_no', $outRequestNo)->refund($orderSn, $orderAmount);
    }


    /**
     * 查询退款
     * @param string $orderSn
     * @param string $refundSn
     * @return AlipayTradeFastpayRefundQueryResponse
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public function queryRefund(string $orderSn, string $refundSn): AlipayTradeFastpayRefundQueryResponse
    {
        return $this->pay->common()->queryRefund($orderSn, $refundSn);
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
        if (isset($result['alipay_trade_precreate_response']['code']) && 10000 != $result['alipay_trade_precreate_response']['code']) {
            throw new \Exception('支付宝:' . $result['alipay_trade_precreate_response']['msg']);
        }
    }


    /**
     * 转账到支付宝账号
     * @param array $withdraw
     * @return array|mixed
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public function transfer(array $withdraw): mixed
    {
        //请求参数
        $data = [
            'out_biz_no' => $withdraw['sn'],//商家侧唯一订单号，由商家自定义。对于不同转账请求，商家需保证该订单号在自身系统唯一。
            'trans_amount' => $withdraw['left_money'],//订单总金额，单位为元，不支持千位分隔符，精确到小数点后两位
            'product_code' => 'TRANS_ACCOUNT_NO_PWD',//销售产品码。单笔无密转账固定为 TRANS_ACCOUNT_NO_PWD。
            'biz_scene' => 'DIRECT_TRANSFER',//业务场景。单笔无密转账固定为 DIRECT_TRANSFER。
            'order_title' => '佣金提现',//转账业务的标题
            'payee_info' => [//收款方信息
                'identity' => $withdraw['account'],//参与方的标识 ID。当 identity_type=ALIPAY_USER_ID 时，填写支付宝用户 UID；当 identity_type=ALIPAY_LOGON_ID 时，填写支付宝登录号。
                'identity_type' => 'ALIPAY_LOGON_ID',//参与方的标识类型。ALIPAY_USER_ID：支付宝会员的用户 ID；ALIPAY_LOGON_ID：支付宝登录号；
                'name' => $withdraw['real_name'],//参与方真实姓名。如果非空，将校验收款支付宝账号姓名一致性。当 identity_type=ALIPAY_LOGON_ID 时，本字段必填。
            ],
            'remark' => '',//业务备注
        ];

        $result = Factory::util()->generic()->execute("alipay.fund.trans.uni.transfer", [], $data);
        $result = json_decode($result->httpBody, true);
        $result = $result['alipay_fund_trans_uni_transfer_response'] ?? [];
        if ($result['code'] != 10000) {//接口调用失败
            throw new \Exception($result['sub_msg'] ?? $result['msg']);
        }
        return $result;
    }


    /**
     * 转账查询
     * @param array $withdraw
     * @return array|mixed
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public function transferQuery(array $withdraw): mixed
    {
        //请求参数
        $data = [
            'out_biz_no' => $withdraw['sn'],//商户转账唯一订单号：发起转账来源方定义的转账单据 ID。
            'product_code' => 'TRANS_ACCOUNT_NO_PWD',//销售产品码，如果传了 out_biz_no，则该字段必传。单笔无密转账固定为TRANS_ACCOUNT_NO_PWD。
            'biz_scene' => 'DIRECT_TRANSFER',//描述特定的业务场景，如果传递了out_biz_no 则该字段为必传。单笔无密转账固定为DIRECT_TRANSFER。
        ];

        $result = Factory::util()->generic()->execute("alipay.fund.trans.common.query", [], $data);
        $result = json_decode($result->httpBody, true);
        return $result['alipay_fund_trans_common_query_response'] ?? [];
    }
}

