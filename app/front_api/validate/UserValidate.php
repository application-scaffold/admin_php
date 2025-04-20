<?php

namespace app\front_api\validate;

use app\common\validate\BaseValidate;

/**
 * 用户验证器
 * @class UserValidate
 * @package app\front_api\validate
 * @author LZH
 * @date 2025/2/20
 */
class UserValidate extends BaseValidate
{

    protected $rule = [
        'code' => 'require',
    ];

    protected $message = [
        'code.require' => '参数缺失',
    ];

    /**
     * 获取小程序手机号场景
     * @return UserValidate
     * @author LZH
     * @date 2025/2/20
     */
    public function sceneGetMobileByMnp()
    {
        return $this->only(['code']);
    }

    /**
     * 绑定/变更 手机号
     * @return UserValidate
     * @author LZH
     * @date 2025/2/20
     */
    public function sceneBindMobile()
    {
        return $this->only(['mobile', 'code']);
    }

}