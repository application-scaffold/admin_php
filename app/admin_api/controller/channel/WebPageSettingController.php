<?php
declare(strict_types=1);

namespace app\admin_api\controller\channel;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\channel\WebPageSettingLogic;
use app\admin_api\validate\channel\WebPageSettingValidate;
use think\response\Json;

/**
 * H5设置控制器
 * @class WebPageSettingController
 * @package app\admin_api\controller\channel
 * @author LZH
 * @date 2025/2/20
 */
class WebPageSettingController extends BaseAdminApiController
{

    /**
     * 获取H5设置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getConfig(): Json
    {
        $result = WebPageSettingLogic::getConfig();
        return $this->data($result);
    }

    /**
     * H5设置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setConfig(): Json
    {
        $params = (new WebPageSettingValidate())->post()->goCheck();
        WebPageSettingLogic::setConfig($params);
        return $this->success('操作成功', [], 1, 1);
    }
}