<?php

namespace app\admin_api\logic\setting\dict;

use app\common\logic\BaseLogic;
use app\common\model\dict\DictData;
use app\common\model\dict\DictType;

/**
 * 字典数据逻辑
 * @class DictDataLogic
 * @package app\admin_api\logic\setting\dict
 * @author LZH
 * @date 2025/2/19
 */
class DictDataLogic extends BaseLogic
{

    /**
     * 添加编辑
     * @param array $params
     * @return DictData|\think\model\contract\Modelable
     * @author LZH
     * @date 2025/2/19
     */
    public static function save(array $params)
    {
        $data = [
            'name' => $params['name'],
            'value' => $params['value'],
            'sort' => $params['sort'] ?? 0,
            'status' => $params['status'],
            'remark' => $params['remark'] ?? '',
        ];

        if (!empty($params['id'])) {
            return DictData::where(['id' => $params['id']])->update($data);
        } else {
            $dictType = DictType::findOrEmpty($params['type_id']);
            $data['type_id'] = $params['type_id'];
            $data['type_value'] = $dictType['type'];
            return DictData::create($data);
        }
    }


    /**
     * 删除字典数据
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function delete(array $params)
    {
        return DictData::destroy($params['id']);
    }


    /**
     * 获取字典数据详情
     * @param $params
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function detail($params): array
    {
        return DictData::findOrEmpty($params['id'])->toArray();
    }

}