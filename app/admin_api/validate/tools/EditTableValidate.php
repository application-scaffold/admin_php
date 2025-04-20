<?php
declare(strict_types=1);

namespace app\admin_api\validate\tools;

use app\common\model\tools\GenerateTable;
use app\common\validate\BaseValidate;

/**
 * 编辑表验证
 * @class EditTableValidate
 * @package app\admin_api\validate\tools
 * @author LZH
 * @date 2025/2/19
 */
class EditTableValidate extends BaseValidate
{

    protected $rule = [
        'id' => 'require|checkTableData',
        'table_name' => 'require',
        'table_comment' => 'require',
        'template_type' => 'require|in:0,1',
        'generate_type' => 'require|in:0,1',
        'module_name' => 'require',
        'table_column' => 'require|array|checkColumn',
    ];

    protected $message = [
        'id.require' => '表id缺失',
        'table_name.require' => '请填写表名称',
        'table_comment.require' => '请填写表描述',
        'template_type.require' => '请选择模板类型',
        'template_type.in' => '模板类型参数错误',
        'generate_type.require' => '请选择生成方式',
        'generate_type.in' => '生成方式类型错误',
        'module_name.require' => '请填写模块名称',
        'table_column.require' => '表字段信息缺失',
        'table_column.array' => '表字段信息类型错误',
    ];


    /**
     * 校验当前数据表是否存在
     * @param string $value
     * @param string $rule
     * @param array $data
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    protected function checkTableData(string $value, string $rule, array $data): bool|string
    {
        $table = GenerateTable::findOrEmpty($value);
        if ($table->isEmpty()) {
            return '信息不存在';
        }
        return true;
    }

    /**
     * 校验表字段参数
     * @param array $value
     * @param string $rule
     * @param array $data
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    protected function checkColumn(array $value, string $rule, array $data): bool|string
    {
        foreach ($value as $item) {
            if (!isset($item['id'])) {
                return '表字段id参数缺失';
            }
            if (!isset($item['query_type'])) {
                return '请选择查询方式';
            }
            if (!isset($item['view_type'])) {
                return '请选择显示类型';
            }
        }
        return true;
    }

}