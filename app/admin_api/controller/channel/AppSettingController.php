<?php
declare(strict_types=1);

namespace app\admin_api\controller\channel;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\channel\AppSettingLogic;
use think\response\Json;

/**
 * APP设置控制器
 * @class AppSettingController
 * @package app\admin_api\controller\channel
 * @author LZH
 * @date 2025/2/20
 */
class AppSettingController extends BaseAdminApiController
{

    /**
     * 获取App设置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getConfig(): Json
    {
        $result = AppSettingLogic::getConfig();
        return $this->data($result);
    }

    /**
     * App设置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setConfig(): Json
    {
        $params = $this->request->post();
        AppSettingLogic::setConfig($params);
        return $this->success('操作成功', [], 1, 1);
    }
}