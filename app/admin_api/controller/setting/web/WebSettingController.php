<?php
declare(strict_types=1);

namespace app\admin_api\controller\setting\web;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\setting\web\WebSettingLogic;
use app\admin_api\validate\setting\WebSettingValidate;
use think\response\Json;

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
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getWebsite(): Json
    {
        $result = WebSettingLogic::getWebsiteInfo();
        return $this->data($result);
    }


    /**
     * 设置网站信息
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setWebsite(): Json
    {
        $params = (new WebSettingValidate())->post()->goCheck('website');
        WebSettingLogic::setWebsiteInfo($params);
        return $this->success('设置成功', [], 1, 1);
    }

    /**
     * 获取备案信息
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getCopyright(): Json
    {
        $result = WebSettingLogic::getCopyright();
        return $this->data($result);
    }

    /**
     * 设置备案信息
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setCopyright(): Json
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
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setAgreement(): Json
    {
        $params = $this->request->post();
        WebSettingLogic::setAgreement($params);
        return $this->success('设置成功', [], 1, 1);
    }

    /**
     * 获取政策协议
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getAgreement(): Json
    {
        $result = WebSettingLogic::getAgreement();
        return $this->data($result);
    }

    /**
     * 获取站点统计配置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getSiteStatistics(): Json
    {
        $result = WebSettingLogic::getSiteStatistics();
        return $this->data($result);
    }

    /**
     * 获取站点统计配置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setSiteStatistics(): Json
    {
        $params = (new WebSettingValidate())->post()->goCheck('siteStatistics');
        WebSettingLogic::setSiteStatistics($params);
        return $this->success('设置成功', [], 1, 1);
    }
}