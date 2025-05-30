<?php
declare(strict_types=1);

namespace app\admin_api\validate\dict;

use app\common\model\dict\DictData;
use app\common\model\dict\DictType;
use app\common\validate\BaseValidate;

/**
 * 字典数据验证
 * @class DictDataValidate
 * @package app\admin_api\validate\dict
 * @author LZH
 * @date 2025/2/19
 */
class DictDataValidate extends BaseValidate
{

    protected $rule = [
        'id' => 'require|checkDictData',
        'name' => 'require|length:1,255',
        'value' => 'require',
        'type_id' => 'require|checkDictType',
        'status' => 'require|in:0,1',
    ];


    protected $message = [
        'id.require' => '参数缺失',
        'name.require' => '请填写字典数据名称',
        'name.length' => '字典数据名称长度须在1-255位字符',
        'value.require' => '请填写字典数据值',
        'type_id.require' => '字典类型缺失',
        'status.require' => '请选择字典数据状态',
        'status.in' => '字典数据状态参数错误',
    ];


    /**
     * 添加场景
     * @return DictDataValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneAdd(): DictDataValidate
    {
        return $this->remove('id', true);
    }


    /**
     * ID场景
     * @return DictDataValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneId(): DictDataValidate
    {
        return $this->only(['id']);
    }


    /**
     * 编辑场景
     * @return DictDataValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneEdit(): DictDataValidate
    {
        return $this->remove('type_id', true);
    }

    /**
     * 校验字典数据
     * @param string $value
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    protected function checkDictData(string $value): bool|string
    {
        $article = DictData::findOrEmpty($value);
        if ($article->isEmpty()) {
            return '字典数据不存在';
        }
        return true;
    }


    /**
     * 校验字典类型
     * @param string $value
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    protected function checkDictType(string $value): bool|string
    {
        $type = DictType::findOrEmpty($value);
        if ($type->isEmpty()) {
            return '字典类型不存在';
        }
        return true;
    }

}