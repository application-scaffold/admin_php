<?php
declare(strict_types=1);

namespace app\admin_api\controller\setting\system;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\setting\system\CacheLogic;
use think\response\Json;

/**
 * 系统缓存
 * @class CacheController
 * @package app\admin_api\controller\setting\system
 * @author LZH
 * @date 2025/2/20
 */
class CacheController extends BaseAdminApiController
{

    /**
     * 清除系统缓存
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function clear(): Json
    {
         CacheLogic::clear();
         return $this->success('清除成功', [], 1, 1);
    }
}