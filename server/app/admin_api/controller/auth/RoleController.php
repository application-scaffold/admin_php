<?php

namespace app\admin_api\controller\auth;

use app\admin_api\{
    logic\auth\RoleLogic,
    lists\auth\RoleLists,
    validate\auth\RoleValidate,
    controller\BaseAdminApiController
};


/**
 * 角色控制器
 * @class RoleController
 * @package app\admin_api\controller\auth
 * @author LZH
 * @date 2025/2/20
 */
class RoleController extends BaseAdminApiController
{

    /**
     * 查看角色列表
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists()
    {
        return $this->dataLists(new RoleLists());
    }


    /**
     * 添加权限
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function add()
    {
        $params = (new RoleValidate())->post()->goCheck('add');
        $res = RoleLogic::add($params);
        if (true === $res) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(RoleLogic::getError());
    }


    /**
     * 编辑角色
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function edit()
    {
        $params = (new RoleValidate())->post()->goCheck('edit');
        $res = RoleLogic::edit($params);
        if (true === $res) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(RoleLogic::getError());
    }

    /**
     * 删除角色
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function delete()
    {
        $params = (new RoleValidate())->post()->goCheck('del');
        RoleLogic::delete($params['id']);
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * 查看角色详情
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function detail()
    {
        $params = (new RoleValidate())->goCheck('detail');
        $detail = RoleLogic::detail($params['id']);
        return $this->data($detail);
    }


    /**
     * 获取角色数据
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function all()
    {
        $result = RoleLogic::getAllData();
        return $this->data($result);
    }

}