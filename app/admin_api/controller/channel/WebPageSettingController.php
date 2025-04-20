<?php

namespace app\admin_api\controller\channel;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\channel\WebPageSettingLogic;
use app\admin_api\validate\channel\WebPageSettingValidate;

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
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getConfig()
    {
        $result = WebPageSettingLogic::getConfig();
        return $this->data($result);
    }

    /**
     * H5设置
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setConfig()
    {
        $params = (new WebPageSettingValidate())->post()->goCheck();
        WebPageSettingLogic::setConfig($params);
        return $this->success('操作成功', [], 1, 1);
    }
}