<?php

namespace app\admin_api\validate\setting;

use app\common\validate\BaseValidate;

/**
 * 存储引擎验证
 * @class StorageValidate
 * @package app\admin_api\validate\setting
 * @author LZH
 * @date 2025/2/19
 */
class StorageValidate extends BaseValidate
{

    protected $rule = [
        'engine' => 'require',
        'status' => 'require',
    ];


    /**
     * 设置存储引擎参数场景
     * @return StorageValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneSetup()
    {
        return $this->only(['engine', 'status']);
    }

    /**
     * 获取配置参数信息场景
     * @return StorageValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDetail()
    {
        return $this->only(['engine']);
    }

    /**
     * 切换存储引擎场景
     * @return StorageValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneChange()
    {
        return $this->only(['engine']);
    }
}