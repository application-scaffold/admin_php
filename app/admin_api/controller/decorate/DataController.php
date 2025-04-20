<?php

namespace app\admin_api\controller\decorate;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\decorate\DecorateDataLogic;
use think\response\Json;

/**
 * 装修-数据
 * @class DataController
 * @package app\admin_api\controller\decorate
 * @author LZH
 * @date 2025/2/20
 */
class DataController extends BaseAdminApiController
{

    /**
     * 文章列表
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function article(): Json
    {
        $limit = $this->request->get('limit/d', 10);
        $result = DecorateDataLogic::getArticleLists($limit);
        return $this->success('获取成功', $result);
    }

    /**
     * pc设置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function pc(): Json
    {
        $result = DecorateDataLogic::pc();
        return $this->data($result);
    }

}