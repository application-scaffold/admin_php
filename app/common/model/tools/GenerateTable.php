<?php
declare (strict_types = 1);

namespace app\common\model\tools;

use app\common\enum\GeneratorEnum;
use app\common\model\BaseModel;
use think\model\relation\HasMany;

/**
 * 代码生成器-数据表信息模型
 * @class GenerateTable
 * @package app\common\model\tools
 * @author LZH
 * @date 2025/2/18
 */
class GenerateTable extends BaseModel
{

    protected array $json = ['menu', 'tree', 'relations', 'delete'];

    protected bool $jsonAssoc = true;

    /**
     * 关联数据表字段
     * @return HasMany
     * @author LZH
     * @date 2025/2/18
     */
    public function tableColumn(): HasMany
    {
        return $this->hasMany(GenerateColumn::class, 'table_id', 'id');
    }

    /**
     * 模板类型描述
     * @param mixed $value
     * @param array $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getTemplateTypeDescAttr(mixed $value, array $data): array|string
    {
        return GeneratorEnum::getTemplateTypeDesc($data['template_type']);
    }

}