<?php
declare(strict_types=1);

namespace app\front_api\validate;

use app\common\validate\BaseValidate;

/**
 * 微信验证器
 * @class WechatValidate
 * @package app\front_api\validate
 * @author LZH
 * @date 2025/2/20
 */
class WechatValidate extends BaseValidate
{
    public $rule = [
        'url' => 'require'
    ];

    public $message = [
        'url.require' => '请提供url'
    ];

    public function sceneJsConfig(): WechatValidate
    {
        return $this->only(['url']);
    }
}