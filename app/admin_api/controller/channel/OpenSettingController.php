<?php
declare(strict_types=1);

namespace app\admin_api\controller\channel;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\channel\OpenSettingLogic;
use app\admin_api\validate\channel\OpenSettingValidate;
use think\response\Json;

/**
 * 微信开放平台
 * @class OpenSettingController
 * @package app\admin_api\controller\channel
 * @author LZH
 * @date 2025/2/20
 */
class OpenSettingController extends BaseAdminApiController
{

    /**
     * 获取微信开放平台设置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getConfig(): Json
    {
        $result = OpenSettingLogic::getConfig();
        return $this->data($result);
    }


    /**
     * 微信开放平台设置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setConfig(): Json
    {
        $params = (new OpenSettingValidate())->post()->goCheck();
        OpenSettingLogic::setConfig($params);
        return $this->success('操作成功', [], 1, 1);
    }
}