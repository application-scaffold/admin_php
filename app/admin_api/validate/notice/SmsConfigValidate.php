<?php
declare(strict_types=1);

namespace app\admin_api\validate\notice;

use app\common\validate\BaseValidate;

/**
 * 短信配置验证
 * @class SmsConfigValidate
 * @package app\admin_api\validate\notice
 * @author LZH
 * @date 2025/2/19
 */
class SmsConfigValidate extends BaseValidate
{
    protected $rule = [
        'type' => 'require',
        'sign' => 'require',
        'app_id' => 'requireIf:type,tencent',
        'app_key' => 'requireIf:type,ali',
        'secret_id' => 'requireIf:type,tencent',
        'secret_key' => 'require',
        'status' => 'require',
    ];

    protected $message = [
        'type.require' => '请选择类型',
        'sign.require' => '请输入签名',
        'app_id.requireIf' => '请输入app_id',
        'app_key.requireIf' => '请输入app_key',
        'secret_id.requireIf' => '请输入secret_id',
        'secret_key.require' => '请输入secret_key',
        'status.require' => '请选择状态',
    ];


    protected function sceneDetail(): SmsConfigValidate
    {
        return $this->only(['type']);
    }
}