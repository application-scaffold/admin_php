<?php

declare (strict_types=1);

namespace app\admin_api\controller;

use app\common\controller\BaseAdminController;

/**
 * 管理元控制器基类
 * @class BaseAdminApiController
 * @package app\admin_api\controller
 * @author LZH
 * @date 2025/2/20
 */
class BaseAdminApiController extends BaseAdminController
{
    protected int $adminId = 0;
    protected array $adminInfo = [];

    public function initialize()
    {
        if (isset($this->request->adminInfo) && $this->request->adminInfo) {
            $this->adminInfo = $this->request->adminInfo;
            $this->adminId = $this->request->adminInfo['admin_id'];
        }
    }

}