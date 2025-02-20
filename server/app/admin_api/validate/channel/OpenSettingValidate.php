<?php

namespace app\admin_api\validate\channel;

use app\common\validate\BaseValidate;

/**
 * 开放平台验证
 * @class OpenSettingValidate
 * @package app\admin_api\validate\channel
 * @author LZH
 * @date 2025/2/19
 */
class OpenSettingValidate extends BaseValidate
{
    protected $rule = [
        'app_id' => 'require',
        'app_secret' => 'require',
    ];

    protected $message = [
        'app_id.require' => '请输入appId',
        'app_secret.require' => '请输入appSecret',
    ];
}