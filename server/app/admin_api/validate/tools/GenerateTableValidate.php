<?php

namespace app\admin_api\validate\tools;

use app\common\model\tools\GenerateTable;
use app\common\validate\BaseValidate;
use think\facade\Db;

/**
 * 代码生成选择表验证
 * @class GenerateTableValidate
 * @package app\admin_api\validate\tools
 * @author LZH
 * @date 2025/2/19
 */
class GenerateTableValidate extends BaseValidate
{

    protected $rule = [
        'id' => 'require|checkTableData',
        'table' => 'require|array|checkTable',
        'file' => 'require'
    ];

    protected $message = [
        'id.require' => '参数缺失',
        'table.require' => '参数缺失',
        'table.array' => '参数类型错误',
        'file.require' => '下载失败',
    ];


    /**
     * 选择数据表场景
     * @return GenerateTableValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneSelect()
    {
        return $this->only(['table']);
    }

    /**
     * 需要校验id的场景
     * @return GenerateTableValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneId()
    {
        return $this->only(['id']);
    }

    /**
     * 下载场景
     * @return GenerateTableValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDownload()
    {
        return $this->only(['file']);
    }

    /**
     * 校验选择的数据表信息
     * @param $value
     * @param $rule
     * @param $data
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    protected function checkTable($value, $rule, $data)
    {
        foreach ($value as $item) {
            if (!isset($item['name']) || !isset($item['comment'])) {
                return '参数缺失';
            }
            $exist = Db::query("SHOW TABLES LIKE'" . $item['name'] . "'");
            if (empty($exist)) {
                return '当前数据库不存在' . $item['name'] . '表';
            }
        }
        return true;
    }


    /**
     * 校验当前数据表是否存在
     * @param $value
     * @param $rule
     * @param $data
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    protected function checkTableData($value, $rule, $data)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        foreach ($value as $item) {
            $table = GenerateTable::findOrEmpty($item);
            if ($table->isEmpty()) {
                return '信息不存在';
            }
        }

        return true;
    }


}