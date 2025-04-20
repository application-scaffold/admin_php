<?php
declare(strict_types=1);

namespace app\admin_api\controller\setting\user;

use app\admin_api\{
    controller\BaseAdminApiController,
    logic\setting\user\UserLogic,
    validate\setting\UserConfigValidate
};
use think\response\Json;


/**
 * 设置-用户设置控制器
 * @class UserController
 * @package app\admin_api\controller\setting\user
 * @author LZH
 * @date 2025/2/20
 */
class UserController extends BaseAdminApiController
{

    /**
     * 获取用户设置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getConfig(): Json
    {
        $result = (new UserLogic())->getConfig();
        return $this->data($result);
    }


    /**
     * 设置用户设置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setConfig(): Json
    {
        $params = (new UserConfigValidate())->post()->goCheck('user');
        (new UserLogic())->setConfig($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * 获取注册配置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getRegisterConfig(): Json
    {
        $result = (new UserLogic())->getRegisterConfig();
        return $this->data($result);
    }

    /**
     * 设置注册配置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setRegisterConfig(): Json
    {
        $params = (new UserConfigValidate())->post()->goCheck('register');
        (new UserLogic())->setRegisterConfig($params);
        return $this->success('操作成功', [], 1, 1);
    }

}