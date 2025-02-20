<?php

namespace app\admin_api\controller\auth;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\auth\AdminLists;
use app\admin_api\validate\auth\AdminValidate;
use app\admin_api\logic\auth\AdminLogic;
use app\admin_api\validate\auth\editSelfValidate;

/**
 * 管理员控制器
 * @class AdminController
 * @package app\admin_api\controller\auth
 * @author LZH
 * @date 2025/2/20
 */
class AdminController extends BaseAdminApiController
{

    /**
     * 查看管理员列表
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists()
    {
        return $this->dataLists(new AdminLists());
    }


    /**
     * 添加管理员
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function add()
    {
        $params = (new AdminValidate())->post()->goCheck('add');
        $result = AdminLogic::add($params);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(AdminLogic::getError());
    }

    /**
     * 编辑管理员
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function edit()
    {
        $params = (new AdminValidate())->post()->goCheck('edit');
        $result = AdminLogic::edit($params);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(AdminLogic::getError());
    }


    /**
     * 删除管理员
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function delete()
    {
        $params = (new AdminValidate())->post()->goCheck('delete');
        $result = AdminLogic::delete($params);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(AdminLogic::getError());
    }


    /**
     * 查看管理员详情
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function detail()
    {
        $params = (new AdminValidate())->goCheck('detail');
        $result = AdminLogic::detail($params);
        return $this->data($result);
    }


    /**
     * 获取当前管理员信息
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function mySelf()
    {
        $result = AdminLogic::detail(['id' => $this->adminId], 'auth');
        return $this->data($result);
    }


    /**
     * 编辑超级管理员信息
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function editSelf()
    {
        $params = (new editSelfValidate())->post()->goCheck('', ['admin_id' => $this->adminId]);
        $result = AdminLogic::editSelf($params);
        return $this->success('操作成功', [], 1, 1);
    }

}