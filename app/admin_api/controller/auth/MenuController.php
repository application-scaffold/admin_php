<?php

namespace app\admin_api\controller\auth;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\auth\MenuLists;
use app\admin_api\logic\auth\MenuLogic;
use app\admin_api\validate\auth\MenuValidate;

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
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function route()
    {
        $result = MenuLogic::getMenuByAdminId($this->adminId);
        return $this->data($result);
    }


    /**
     * 获取菜单列表
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists()
    {
        return $this->dataLists(new MenuLists());
    }


    /**
     * 菜单详情
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail()
    {
        $params = (new MenuValidate())->goCheck('detail');
        return $this->data(MenuLogic::detail($params));
    }


    /**
     * 添加菜单
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function add()
    {
        $params = (new MenuValidate())->post()->goCheck('add');
        MenuLogic::add($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * 编辑菜单
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function edit()
    {
        $params = (new MenuValidate())->post()->goCheck('edit');
        MenuLogic::edit($params);
        return $this->success('操作成功', [], 1, 1);
    }


    /**
     * 删除菜单
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function delete()
    {
        $params = (new MenuValidate())->post()->goCheck('delete');
        MenuLogic::delete($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * 更新状态
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function updateStatus()
    {
        $params = (new MenuValidate())->post()->goCheck('status');
        MenuLogic::updateStatus($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * 获取菜单数据
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function all()
    {
        $result = MenuLogic::getAllData();
        return $this->data($result);
    }

}