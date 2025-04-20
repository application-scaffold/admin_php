<?php

namespace app\admin_api\controller\channel;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\channel\OfficialAccountSettingLogic;
use app\admin_api\validate\channel\OfficialAccountSettingValidate;

/**
 * 公众号设置
 * @class OfficialAccountSettingController
 * @package app\admin_api\controller\channel
 * @author LZH
 * @date 2025/2/20
 */
class OfficialAccountSettingController extends BaseAdminApiController
{
    /**
     * 获取公众号配置
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getConfig()
    {
        $result = (new OfficialAccountSettingLogic())->getConfig();
        return $this->data($result);
    }

    /**
     * 设置公众号配置
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setConfig()
    {
        $params = (new OfficialAccountSettingValidate())->post()->goCheck();
        (new OfficialAccountSettingLogic())->setConfig($params);
        return $this->success('操作成功',[],1,1);
    }
}