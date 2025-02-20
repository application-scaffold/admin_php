<?php

namespace app\admin_api\controller\channel;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\channel\MnpSettingsLogic;
use app\admin_api\validate\channel\MnpSettingsValidate;

/**
 * 小程序设置
 * @class MnpSettingsController
 * @package app\admin_api\controller\channel
 * @author LZH
 * @date 2025/2/20
 */
class MnpSettingsController extends BaseAdminApiController
{

    /**
     * 获取小程序配置
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getConfig()
    {
        $result = (new MnpSettingsLogic())->getConfig();
        return $this->data($result);
    }

    /**
     * 设置小程序配置
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setConfig()
    {
        $params = (new MnpSettingsValidate())->post()->goCheck();
        (new MnpSettingsLogic())->setConfig($params);
        return $this->success('操作成功', [], 1, 1);
    }
}