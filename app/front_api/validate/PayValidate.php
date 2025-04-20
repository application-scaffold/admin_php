<?php
declare(strict_types=1);

namespace app\front_api\validate;

use app\common\enum\PayEnum;
use app\common\validate\BaseValidate;

/**
 * 支付验证
 * @class PayValidate
 * @package app\front_api\validate
 * @author LZH
 * @date 2025/2/20
 */
class PayValidate extends BaseValidate
{
    protected $rule = [
        'from'      => 'require',
        'pay_way'   => 'require|in:' . PayEnum::BALANCE_PAY . ',' . PayEnum::WECHAT_PAY . ',' . PayEnum::ALI_PAY,
        'order_id'  => 'require'
    ];


    protected $message = [
        'from.require'      => '参数缺失',
        'pay_way.require'   => '支付方式参数缺失',
        'pay_way.in'        => '支付方式参数错误',
        'order_id.require'  => '订单参数缺失'
    ];


    /**
     * 支付方式场景
     * @return PayValidate
     * @author LZH
     * @date 2025/2/20
     */
    public function scenePayway(): PayValidate
    {
        return $this->only(['from', 'order_id']);
    }

    /**
     * 支付状态
     * @return PayValidate
     * @author LZH
     * @date 2025/2/20
     */
    public function sceneStatus(): PayValidate
    {
        return $this->only(['from', 'order_id']);
    }

}