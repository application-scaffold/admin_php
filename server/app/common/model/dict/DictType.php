<?php

namespace app\common\model\dict;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 字典类型模型
 * @class DictType
 * @package app\common\model\dict
 * @author LZH
 * @date 2025/2/18
 */
class DictType extends BaseModel
{

    use SoftDelete;

    protected $deleteTime = 'delete_time';

    /**
     * 状态描述
     * @param $value
     * @param $data
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getStatusDescAttr($value, $data)
    {
        return $data['status'] ? '正常' : '停用';
    }

}