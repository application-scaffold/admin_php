<?php

namespace app\front_api\validate;

use app\common\validate\BaseValidate;

/**
 * 短信验证
 * @class SendSmsValidate
 * @package app\front_api\validate
 * @author LZH
 * @date 2025/2/20
 */
class SendSmsValidate extends BaseValidate
{

    protected $rule = [
        'mobile' => 'require|mobile',
        'scene' => 'require',
    ];

    protected $message = [
        'mobile.require' => '请输入手机号',
        'mobile.mobile' => '请输入正确手机号',
        'scene.require' => '请输入场景值',
    ];
}