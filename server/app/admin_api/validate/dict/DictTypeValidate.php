<?php

namespace app\admin_api\validate\dict;


use app\common\model\dict\DictData;
use app\common\model\dict\DictType;
use app\common\validate\BaseValidate;

/**
 * 字典类型验证
 * @class DictTypeValidate
 * @package app\admin_api\validate\dict
 * @author LZH
 * @date 2025/2/19
 */
class DictTypeValidate extends BaseValidate
{
    
    protected $rule = [
        'id' => 'require|checkDictType',
        'name' => 'require|length:1,255',
        'type' => 'require|unique:' . DictType::class,
        'status' => 'require|in:0,1',
        'remark' => 'max:200',
    ];


    protected $message = [
        'id.require' => '参数缺失',
        'name.require' => '请填写字典名称',
        'name.length' => '字典名称长度须在1~255位字符',
        'type.require' => '请填写字典类型',
        'type.unique' => '字典类型已存在',
        'status.require' => '请选择状态',
        'remark.max' => '备注长度不能超过200',
    ];

    /**
     * 添加场景
     * @return DictTypeValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneAdd()
    {
        return $this->remove('id', true);
    }

    /**
     * 详情场景
     * @return DictTypeValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }
    

    public function sceneEdit()
    {
    }


    /**
     * 删除场景
     * @return DictTypeValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDelete()
    {
        return $this->only(['id'])
            ->append('id', 'checkAbleDelete');
    }

    /**
     * 检查字典类型是否存在
     * @param $value
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    protected function checkDictType($value)
    {
        $dictType = DictType::findOrEmpty($value);
        if ($dictType->isEmpty()) {
            return '字典类型不存在';
        }
        return true;
    }

    /**
     * 验证是否可删除
     * @param $value
     * @return string|true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    protected function checkAbleDelete($value)
    {
        $dictData = DictData::whereIn('type_id', $value)->select();

        foreach ($dictData as $item) {
            if (!empty($item)) {
                return '字典类型已被使用，请先删除绑定该字典类型的数据';
            }
        }

        return true;
    }

}