<?php

namespace app\admin_api\controller\notice;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\notice\SmsConfigLogic;
use app\admin_api\validate\notice\SmsConfigValidate;

/**
 * 短信配置控制器
 * @class SmsConfigController
 * @package app\admin_api\controller\notice
 * @author LZH
 * @date 2025/2/20
 */
class SmsConfigController extends BaseAdminApiController
{

    /**
     * 获取短信配置
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getConfig()
    {
        $result = SmsConfigLogic::getConfig();
        return $this->data($result);
    }

    /**
     * 短信配置
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setConfig()
    {
        $params = (new SmsConfigValidate())->post()->goCheck('setConfig');
        SmsConfigLogic::setConfig($params);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * 查看短信配置详情
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail()
    {
        $params = (new SmsConfigValidate())->goCheck('detail');
        $result = SmsConfigLogic::detail($params);
        return $this->data($result);
    }

}