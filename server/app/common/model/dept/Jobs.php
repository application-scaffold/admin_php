<?php

namespace app\common\model\dept;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 岗位模型
 * @class Jobs
 * @package app\common\model\dept
 * @author LZH
 * @date 2025/2/18
 */
class Jobs extends BaseModel
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