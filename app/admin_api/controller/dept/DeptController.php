<?php
declare(strict_types=1);

namespace app\admin_api\controller\dept;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\dept\DeptLogic;
use app\admin_api\validate\dept\DeptValidate;
use think\response\Json;

/**
 * 部门管理控制器
 * @class DeptController
 * @package app\admin_api\controller\dept
 * @author LZH
 * @date 2025/2/20
 */
class DeptController extends BaseAdminApiController
{

    /**
     * 部门列表
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function lists(): Json
    {
        $params = $this->request->get();
        $result = DeptLogic::lists($params);
        return $this->success('',$result);
    }


    /**
     * 上级部门
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function leaderDept(): Json
    {
        $result = DeptLogic::leaderDept();
        return $this->success('',$result);
    }


    /**
     * 添加部门
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function add(): Json
    {
        $params = (new DeptValidate())->post()->goCheck('add');
        DeptLogic::add($params);
        return $this->success('添加成功', [], 1, 1);
    }


    /**
     * 编辑部门
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function edit(): Json
    {
        $params = (new DeptValidate())->post()->goCheck('edit');
        $result = DeptLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(DeptLogic::getError());
    }

    /**
     * 删除部门
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function delete(): Json
    {
        $params = (new DeptValidate())->post()->goCheck('delete');
        DeptLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * 获取部门详情
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail(): Json
    {
        $params = (new DeptValidate())->goCheck('detail');
        $result = DeptLogic::detail($params);
        return $this->data($result);
    }


    /**
     * 获取部门数据
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function all(): Json
    {
        $result = DeptLogic::getAllData();
        return $this->data($result);
    }

}