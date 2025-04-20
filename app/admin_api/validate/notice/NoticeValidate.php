<?php

namespace app\admin_api\validate\notice;

use app\common\validate\BaseValidate;

/**
 * 通知验证
 * @class NoticeValidate
 * @package app\admin_api\validate\notice
 * @author LZH
 * @date 2025/2/19
 */
class NoticeValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require',
    ];

    protected $message = [
        'id.require' => '参数缺失',
    ];

    protected function sceneDetail()
    {
        return $this->only(['id']);
    }

}