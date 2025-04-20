<?php

namespace app\front_api\controller;

use app\front_api\logic\PcLogic;
use think\response\Json;

/**
 * @class PcController
 * @package app\front_api\controller
 * @author LZH
 * @date 2025/2/19
 */
class PcController extends BaseApiController
{

    public array $notNeedLogin = ['index', 'config', 'infoCenter', 'articleDetail'];

    /**
     * 首页数据
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function index()
    {
        $result = PcLogic::getIndexData();
        return $this->data($result);
    }

    /**
     * 全局配置
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function config()
    {
        $result = PcLogic::getConfigData();
        return $this->data($result);
    }

    /**
     * 资讯中心
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function infoCenter()
    {
        $result = PcLogic::getInfoCenter();
        return $this->data($result);
    }

    /**
     * 获取文章详情
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function articleDetail()
    {
        $id = $this->request->get('id/d', 0);
        $source = $this->request->get('source/s', 'default');
        $result = PcLogic::getArticleDetail($this->userId, $id, $source);
        return $this->data($result);
    }

}