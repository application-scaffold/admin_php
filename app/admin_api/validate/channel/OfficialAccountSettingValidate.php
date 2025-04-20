<?php

namespace app\admin_api\validate\channel;

use app\common\validate\BaseValidate;

/**
 * 公众号设置
 * @class OfficialAccountSettingValidate
 * @package app\admin_api\validate\channel
 * @author LZH
 * @date 2025/2/19
 */
class OfficialAccountSettingValidate extends BaseValidate
{
    protected $rule = [
        'app_id' => 'require',
        'app_secret' => 'require',
        'encryption_type' => 'require|in:1,2,3',
    ];

    protected $message = [
        'app_id.require' => '请填写AppID',
        'app_secret.require' => '请填写AppSecret',
        'encryption_type.require' => '请选择消息加密方式',
        'encryption_type.in' => '消息加密方式状态值错误',
    ];
}