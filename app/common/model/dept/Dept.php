<?php
declare (strict_types = 1);

namespace app\common\model\dept;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 部门模型
 * @class Dept
 * @package app\common\model\dept
 * @author LZH
 * @date 2025/2/18
 */
class Dept extends BaseModel
{

    use SoftDelete;

    protected string $deleteTime = 'delete_time';

    /**
     * 状态描述
     * @param mixed $value
     * @param array $data
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getStatusDescAttr(mixed $value, array $data): string
    {
        return $data['status'] ? '正常' : '停用';
    }

}