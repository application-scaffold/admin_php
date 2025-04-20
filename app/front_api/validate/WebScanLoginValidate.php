<?php

namespace app\front_api\validate;

use app\common\cache\WebScanLoginCache;
use app\common\validate\BaseValidate;

/**
 * 网站扫码登录验证
 * @class WebScanLoginValidate
 * @package app\front_api\validate
 * @author LZH
 * @date 2025/2/20
 */
class WebScanLoginValidate extends BaseValidate
{

    protected $rule = [
        'code' => 'require',
        'state' => 'require|checkState',
    ];

    protected $message = [
        'code.require' => '参数缺失',
        'state.require' => '昵称缺少',
    ];


    /**
     * 校验登录状态标记
     * @param $value
     * @param $rule
     * @param $data
     * @return string|true
     * @author LZH
     * @date 2025/2/20
     */
    protected function checkState($value, $rule, $data)
    {
        $check = (new WebScanLoginCache())->getScanLoginState($value);

        if (empty($check)) {
            return '二维码已失效或不存在,请重新扫码';
        }

        return true;
    }

}