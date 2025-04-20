<?php

namespace app\common\model\tools;

use app\common\enum\GeneratorEnum;
use app\common\model\BaseModel;

/**
 * 代码生成器-数据表信息模型
 * @class GenerateTable
 * @package app\common\model\tools
 * @author LZH
 * @date 2025/2/18
 */
class GenerateTable extends BaseModel
{

    protected $json = ['menu', 'tree', 'relations', 'delete'];

    protected $jsonAssoc = true;

    /**
     * 关联数据表字段
     * @return \think\model\relation\HasMany
     * @author LZH
     * @date 2025/2/18
     */
    public function tableColumn()
    {
        return $this->hasMany(GenerateColumn::class, 'table_id', 'id');
    }

    /**
     * 模板类型描述
     * @param $value
     * @param $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getTemplateTypeDescAttr($value, $data)
    {
        return GeneratorEnum::getTemplateTypeDesc($data['template_type']);
    }

}