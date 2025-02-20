<?php

namespace app\admin_api\controller;

use app\admin_api\logic\WorkbenchLogic;

/**
 * 工作台
 * @class WorkbenchController
 * @package app\admin_api\controller
 * @author LZH
 * @date 2025/2/20
 */
class WorkbenchController extends BaseAdminApiController
{

    /**
     * 工作台
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function index()
    {
        $result = WorkbenchLogic::index();
        return $this->data($result);
    }
}