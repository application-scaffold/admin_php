<?php
declare(strict_types=1);

namespace app\admin_api\controller\user;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\user\UserLists;
use app\admin_api\logic\user\UserLogic;
use app\admin_api\validate\user\AdjustUserMoney;
use app\admin_api\validate\user\UserValidate;
use think\response\Json;

/**
 * 用户控制器
 * @class UserController
 * @package app\admin_api\controller\user
 * @author LZH
 * @date 2025/2/20
 */
class UserController extends BaseAdminApiController
{

    /**
     * 用户列表
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists(): Json
    {
        return $this->dataLists(new UserLists());
    }

    /**
     * 获取用户详情
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail(): Json
    {
        $params = (new UserValidate())->goCheck('detail');
        $detail = UserLogic::detail($params['id']);
        return $this->success('', $detail);
    }


    /**
     * 编辑用户信息
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function edit(): Json
    {
        $params = (new UserValidate())->post()->goCheck('setInfo');
        UserLogic::setUserInfo($params);
        return $this->success('操作成功', [], 1, 1);
    }


    /**
     * 调整用户余额
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function adjustMoney(): Json
    {
        $params = (new AdjustUserMoney())->post()->goCheck();
        $res = UserLogic::adjustUserMoney($params);
        if (true === $res) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail($res);
    }

}