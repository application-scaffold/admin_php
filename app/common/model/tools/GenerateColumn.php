<?php
declare (strict_types = 1);

namespace app\common\model\tools;

use app\common\model\BaseModel;
use think\model\relation\BelongsTo;

/**
 * 代码生成器-数据表字段信息模型
 * @class GenerateColumn
 * @package app\common\model\tools
 * @author LZH
 * @date 2025/2/18
 */
class GenerateColumn extends BaseModel
{

    /**
     * 关联table表
     * @return BelongsTo
     * @author LZH
     * @date 2025/2/18
     */
    public function generateTable(): BelongsTo
    {
        return $this->belongsTo(GenerateTable::class, 'id', 'table_id');
    }
}