<?php

namespace app\front_api\controller;


use app\front_api\logic\IndexLogic;
use think\response\Json;


/**
 * index
 * @class IndexController
 * @package app\front_api\controller
 * @author LZH
 * @date 2025/2/19
 */
class IndexController extends BaseApiController
{

    public array $notNeedLogin = ['index', 'config', 'policy', 'decorate'];

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
        $result = IndexLogic::getIndexData();
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
        $result = IndexLogic::getConfigData();
        return $this->data($result);
    }


    /**
     * 政策协议
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function policy()
    {
        $type = $this->request->get('type/s', '');
        $result = IndexLogic::getPolicyByType($type);
        return $this->data($result);
    }

    /**
     * 装修信息
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function decorate()
    {
        $id = $this->request->get('id/d');
        $result = IndexLogic::getDecorate($id);
        return $this->data($result);
    }

}