<?php

namespace app\admin_api\logic\dept;

use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\article\Article;
use app\common\model\dept\Jobs;
use app\common\service\FileService;

/**
 * 岗位管理逻辑
 * @class JobsLogic
 * @package app\admin_api\logic\dept
 * @author LZH
 * @date 2025/2/19
 */
class JobsLogic extends BaseLogic
{

    /**
     * 新增岗位
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function add(array $params)
    {
        Jobs::create([
            'name' => $params['name'],
            'code' => $params['code'],
            'sort' => $params['sort'] ?? 0,
            'status' => $params['status'],
            'remark' => $params['remark'] ?? '',
        ]);
    }

    /**
     * 编辑岗位
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function edit(array $params) : bool
    {
        try {
            Jobs::update([
                'id' => $params['id'],
                'name' => $params['name'],
                'code' => $params['code'],
                'sort' => $params['sort'] ?? 0,
                'status' => $params['status'],
                'remark' => $params['remark'] ?? '',
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * 删除岗位
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function delete(array $params)
    {
        Jobs::destroy($params['id']);
    }

    /**
     * 获取岗位详情
     * @param $params
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function detail($params) : array
    {
        return Jobs::findOrEmpty($params['id'])->toArray();
    }

    /**
     * 岗位数据
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function getAllData()
    {
        return Jobs::where(['status' => YesNoEnum::YES])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();
    }

}