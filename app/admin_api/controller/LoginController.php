<?php
declare(strict_types=1);

namespace app\admin_api\controller;

use app\admin_api\logic\LoginLogic;
use app\admin_api\validate\LoginValidate;
use think\response\Json;

/**
 * 管理员登录控制器
 * @class LoginController
 * @package app\admin_api\controller
 * @author LZH
 * @date 2025/2/20
 */
class LoginController extends BaseAdminApiController
{
    public array $notNeedLogin = ['account'];

    /**
     * 账号登录
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function account(): Json
    {
        $params = (new LoginValidate())->post()->goCheck();
        return $this->data((new LoginLogic())->login($params));
    }

    /**
     * 退出登录
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function logout(): Json
    {
        //退出登录情况特殊，只有成功的情况，也不需要token验证
        (new LoginLogic())->logout($this->adminInfo);
        return $this->success();
    }
}