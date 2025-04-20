<?php
declare(strict_types=1);

namespace app\admin_api\controller\setting\system;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\setting\system\SystemLogic;
use think\response\Json;


/**
 * 系统维护
 * @class SystemController
 * @package app\admin_api\controller\setting\system
 * @author LZH
 * @date 2025/2/20
 */
class SystemController extends BaseAdminApiController
{

    /**
     * 获取系统环境信息
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function info(): Json
    {
        $result = SystemLogic::getInfo();
        return $this->data($result);
    }

}