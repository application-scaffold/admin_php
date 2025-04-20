<?php
declare(strict_types=1);

namespace app\admin_api\controller\auth;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\auth\MenuLists;
use app\admin_api\logic\auth\MenuLogic;
use app\admin_api\validate\auth\MenuValidate;
use think\response\Json;

/**
 * 系统菜单权限
 * @class MenuController
 * @package app\admin_api\controller\auth
 * @author LZH
 * @date 2025/2/20
 */
class MenuController extends BaseAdminApiController
{

    /**
     * 获取菜单路由
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function route(): Json
    {
        $result = MenuLogic::getMenuByAdminId($this->adminId);
        return $this->data($result);
    }

    /**
     * 获取菜单列表
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists(): Json
    {
        return $this->dataLists(new MenuLists());
    }


    /**
     * 菜单详情
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail(): Json
    {
        $params = (new MenuValidate())->goCheck('detail');
        return $this->data(MenuLogic::detail($params));
    }


    /**
     * 添加菜单
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function add(): Json
    {
        $params = (new MenuValidate())->post()->goCheck('add');
        MenuLogic::add($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * 编辑菜单
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function edit(): Json
    {
        $params = (new MenuValidate())->post()->goCheck('edit');
        MenuLogic::edit($params);
        return $this->success('操作成功', [], 1, 1);
    }


    /**
     * 删除菜单
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function delete(): Json
    {
        $params = (new MenuValidate())->post()->goCheck('delete');
        MenuLogic::delete($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * 更新状态
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function updateStatus(): Json
    {
        $params = (new MenuValidate())->post()->goCheck('status');
        MenuLogic::updateStatus($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * 获取菜单数据
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function all(): Json
    {
        $result = MenuLogic::getAllData();
        return $this->data($result);
    }

}