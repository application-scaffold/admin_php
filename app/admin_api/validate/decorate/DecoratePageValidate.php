<?php
declare(strict_types=1);

namespace app\admin_api\validate\decorate;

use app\common\validate\BaseValidate;

/**
 * 装修页面验证
 * @class DecoratePageValidate
 * @package app\admin_api\validate\decorate
 * @author LZH
 * @date 2025/2/19
 */
class DecoratePageValidate extends BaseValidate
{

    protected $rule = [
        'id' => 'require',
        'type' => 'require',
        'data' => 'require',
    ];


    protected $message = [
        'id.require' => '参数缺失',
        'type.require' => '装修类型参数缺失',
        'data.require' => '装修信息参数缺失',
    ];

}