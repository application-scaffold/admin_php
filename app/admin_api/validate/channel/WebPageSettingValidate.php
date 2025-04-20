<?php
declare(strict_types=1);

namespace app\admin_api\validate\channel;

use app\common\validate\BaseValidate;

/**
 * H5设置验证器
 * @class WebPageSettingValidate
 * @package app\admin_api\validate\channel
 * @author LZH
 * @date 2025/2/19
 */
class WebPageSettingValidate extends BaseValidate
{
    protected $rule = [
        'status' => 'require|in:0,1'
    ];

    protected $message = [
        'status.require' => '请选择启用状态',
        'status.in' => '启用状态值有误',
    ];
}