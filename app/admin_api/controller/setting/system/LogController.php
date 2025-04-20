<?php
declare(strict_types=1);

namespace app\admin_api\controller\setting\system;


use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\setting\system\LogLists;
use think\response\Json;

/**
 * 系统日志
 * @class LogController
 * @package app\admin_api\controller\setting\system
 * @author LZH
 * @date 2025/2/20
 */
class LogController extends BaseAdminApiController
{

    /**
     * 查看系统日志列表
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists(): Json
    {
        return $this->dataLists(new LogLists());
    }
}