<?php
declare(strict_types=1);

namespace app\admin_api\controller\crontab;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\crontab\CrontabLists;
use app\admin_api\logic\crontab\CrontabLogic;
use app\admin_api\validate\crontab\CrontabValidate;
use think\response\Json;

/**
 * 定时任务控制器
 * @class CrontabController
 * @package app\admin_api\controller\crontab
 * @author LZH
 * @date 2025/2/20
 */
class CrontabController extends BaseAdminApiController
{

    /**
     * 定时任务列表
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists(): Json
    {
        return $this->dataLists(new CrontabLists());
    }


    /**
     * 添加定时任务
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function add(): Json
    {
        $params = (new CrontabValidate())->post()->goCheck('add');
        $result = CrontabLogic::add($params);
        if($result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(CrontabLogic::getError());
    }


    /**
     * 查看定时任务详情
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail(): Json
    {
        $params = (new CrontabValidate())->goCheck('detail');
        $result = CrontabLogic::detail($params);
        return $this->data($result);
    }


    /**
     * 编辑定时任务
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function edit(): Json
    {
        $params = (new CrontabValidate())->post()->goCheck('edit');
        $result = CrontabLogic::edit($params);
        if($result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(CrontabLogic::getError());
    }


    /**
     * @notes 删除定时任务
     * @return Json
     * @author 段誉
     * @date 2022/3/29 14:27
     */
    /**
     * 删除定时任务
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function delete(): Json
    {
        $params = (new CrontabValidate())->post()->goCheck('delete');
        $result = CrontabLogic::delete($params);
        if($result) {
            return $this->success('删除成功', [], 1, 1);
        }
        return $this->fail('删除失败');
    }

    /**
     * 操作定时任务
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function operate(): Json
    {
        $params = (new CrontabValidate())->post()->goCheck('operate');
        $result = CrontabLogic::operate($params);
        if($result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(CrontabLogic::getError());
    }


    /**
     * 获取规则执行时间
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function expression(): Json
    {
        $params = (new CrontabValidate())->goCheck('expression');
        $result = CrontabLogic::expression($params);
        return $this->data($result);
    }
}