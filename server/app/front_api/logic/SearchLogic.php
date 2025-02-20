<?php

namespace app\front_api\logic;

use app\common\logic\BaseLogic;
use app\common\model\HotSearch;
use app\common\service\ConfigService;

/**
 * 搜索逻辑
 * @class SearchLogic
 * @package app\front_api\logic
 * @author LZH
 * @date 2025/2/20
 */
class SearchLogic extends BaseLogic
{

    /**
     * 热搜列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public static function hotLists()
    {
        $data = HotSearch::field(['name', 'sort'])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()->toArray();

        return [
            // 功能状态 0-关闭 1-开启
            'status' => ConfigService::get('hot_search', 'status', 0),
            // 热门搜索数据
            'data' => $data,
        ];
    }

}