<?php
declare(strict_types=1);

namespace app\admin_api\controller\setting;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\setting\CustomerServiceLogic;
use think\response\Json;

/**
 * 客服设置
 * @class CustomerServiceController
 * @package app\admin_api\controller\setting
 * @author LZH
 * @date 2025/2/20
 */
class CustomerServiceController extends BaseAdminApiController
{

    /**
     * 获取客服设置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getConfig(): Json
    {
        $result = CustomerServiceLogic::getConfig();
        return $this->data($result);
    }

    /**
     * 设置客服设置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setConfig(): Json
    {
        $params = $this->request->post();
        CustomerServiceLogic::setConfig($params);
        return $this->success('设置成功', [], 1, 1);
    }
}