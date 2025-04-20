<?php
declare(strict_types=1);

namespace app\admin_api\validate\channel;


use app\common\validate\BaseValidate;

/**
 * 小程序设置
 * @class MnpSettingsValidate
 * @package app\admin_api\validate\channel
 * @author LZH
 * @date 2025/2/19
 */
class MnpSettingsValidate extends BaseValidate
{
    protected $rule = [
        'app_id' => 'require',
        'app_secret' => 'require',
    ];

    protected $message = [
        'app_id.require' => '请填写AppID',
        'app_secret.require' => '请填写AppSecret',
    ];
}