<?php
declare(strict_types=1);

namespace app\front_api\controller;

use app\front_api\validate\PayValidate;
use app\common\enum\user\UserTerminalEnum;
use app\common\logic\PaymentLogic;
use app\common\service\pay\AliPayService;
use app\common\service\pay\WeChatPayService;
use think\response\Json;

/**
 * 支付
 * @class PayController
 * @package app\front_api\controller
 * @author LZH
 * @date 2025/2/19
 */
class PayController extends BaseApiController
{

    public array $notNeedLogin = ['notifyMnp', 'notifyOa', 'aliNotify'];

    /**
     * 支付方式
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function payWay(): Json
    {
        $params = (new PayValidate())->goCheck('payway');
        $result = PaymentLogic::getPayWay($this->userId, $this->userInfo['terminal'], $params);
        if ($result === false) {
            return $this->fail(PaymentLogic::getError());
        }
        return $this->data($result);
    }

    /**
     * 预支付
     * @return Json
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public function prepay(): Json
    {
        $params = (new PayValidate())->post()->goCheck();
        //订单信息
        $order = PaymentLogic::getPayOrderInfo($params);
        if (false === $order) {
            return $this->fail(PaymentLogic::getError(), $params);
        }
        //支付流程
        $redirectUrl = $params['redirect'] ?? '/pages/payment/payment';
        $result = PaymentLogic::pay($params['pay_way'], $params['from'], $order, $this->userInfo['terminal'], $redirectUrl);
        if (false === $result) {
            return $this->fail(PaymentLogic::getError(), $params);
        }
        return $this->success('', $result);
    }

    /**
     * 获取支付状态
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function payStatus(): Json
    {
        $params = (new PayValidate())->goCheck('status', ['user_id' => $this->userId]);
        $result = PaymentLogic::getPayStatus($params);
        if ($result === false) {
            return $this->fail(PaymentLogic::getError());
        }
        return $this->data($result);
    }


    /**
     * 小程序支付回调
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function notifyMnp(): mixed
    {
        return (new WeChatPayService(UserTerminalEnum::WECHAT_MMP))->notify();
    }

    /**
     * 公众号支付回调
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function notifyOa(): mixed
    {
        return (new WeChatPayService(UserTerminalEnum::WECHAT_OA))->notify();
    }

    /**
     * 支付宝回调
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public function aliNotify(): void
    {
        $params = $this->request->post();
        $result = (new AliPayService())->notify($params);
        if (true === $result) {
            echo 'success';
        } else {
            echo 'fail';
        }
    }

}
