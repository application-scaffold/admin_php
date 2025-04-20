<?php
declare(strict_types=1);

namespace app\admin_api\controller\dept;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\dept\JobsLists;
use app\admin_api\logic\dept\JobsLogic;
use app\admin_api\validate\dept\JobsValidate;
use think\response\Json;


/**
 * 岗位管理控制器
 * @class JobsController
 * @package app\admin_api\controller\dept
 * @author LZH
 * @date 2025/2/20
 */
class JobsController extends BaseAdminApiController
{

    /**
     * 岗位列表
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists(): Json
    {
        return $this->dataLists(new JobsLists());
    }

    /**
     * 添加岗位
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function add(): Json
    {
        $params = (new JobsValidate())->post()->goCheck('add');
        JobsLogic::add($params);
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * 编辑岗位
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function edit(): Json
    {
        $params = (new JobsValidate())->post()->goCheck('edit');
        $result = JobsLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(JobsLogic::getError());
    }

    /**
     * 删除岗位
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function delete(): Json
    {
        $params = (new JobsValidate())->post()->goCheck('delete');
        JobsLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * 获取岗位详情
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail(): Json
    {
        $params = (new JobsValidate())->goCheck('detail');
        $result = JobsLogic::detail($params);
        return $this->data($result);
    }

    /**
     * 获取岗位数据
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function all(): Json
    {
        $result = JobsLogic::getAllData();
        return $this->data($result);
    }

}