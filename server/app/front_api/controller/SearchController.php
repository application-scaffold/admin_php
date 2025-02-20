<?php

namespace app\front_api\controller;

use app\front_api\logic\SearchLogic;

/**
 * 搜索
 * @class SearchController
 * @package app\front_api\controller
 * @author LZH
 * @date 2025/2/19
 */
class SearchController extends BaseApiController
{

    public array $notNeedLogin = ['hotLists'];

    /**
     * 热门搜素
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function hotLists()
    {
        return $this->data(SearchLogic::hotLists());
    }

}