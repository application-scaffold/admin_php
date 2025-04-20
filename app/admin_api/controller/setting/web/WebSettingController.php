<?php

namespace app\admin_api\controller\setting\web;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\setting\web\WebSettingLogic;
use app\admin_api\validate\setting\WebSettingValidate;

/**
 * 网站设置
 * @class WebSettingController
 * @package app\admin_api\controller\setting\web
 * @author LZH
 * @date 2025/2/20
 */
class WebSettingController extends BaseAdminApiController
{

    /**
     * 获取网站信息
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getWebsite()
    {
        $result = WebSettingLogic::getWebsiteInfo();
        return $this->data($result);
    }


    /**
     * 设置网站信息
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setWebsite()
    {
        $params = (new WebSettingValidate())->post()->goCheck('website');
        WebSettingLogic::setWebsiteInfo($params);
        return $this->success('设置成功', [], 1, 1);
    }

    /**
     * 获取备案信息
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getCopyright()
    {
        $result = WebSettingLogic::getCopyright();
        return $this->data($result);
    }

    /**
     * 设置备案信息
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setCopyright()
    {
        $params = $this->request->post();
        $result = WebSettingLogic::setCopyright($params);
        if (false === $result) {
            return $this->fail(WebSettingLogic::getError() ?: '操作失败');
        }
        return $this->success('设置成功', [], 1, 1);
    }

    /**
     * 设置政策协议
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setAgreement()
    {
        $params = $this->request->post();
        WebSettingLogic::setAgreement($params);
        return $this->success('设置成功', [], 1, 1);
    }

    /**
     * 获取政策协议
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getAgreement()
    {
        $result = WebSettingLogic::getAgreement();
        return $this->data($result);
    }

    /**
     * 获取站点统计配置
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getSiteStatistics()
    {
        $result = WebSettingLogic::getSiteStatistics();
        return $this->data($result);
    }

    /**
     * 获取站点统计配置
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setSiteStatistics()
    {
        $params = (new WebSettingValidate())->post()->goCheck('siteStatistics');
        WebSettingLogic::setSiteStatistics($params);
        return $this->success('设置成功', [], 1, 1);
    }
}