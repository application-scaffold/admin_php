<?php

namespace app\admin_api\controller\crontab;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\crontab\CrontabLists;
use app\admin_api\logic\crontab\CrontabLogic;
use app\admin_api\validate\crontab\CrontabValidate;

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
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists()
    {
        return $this->dataLists(new CrontabLists());
    }


    /**
     * 添加定时任务
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function add()
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
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail()
    {
        $params = (new CrontabValidate())->goCheck('detail');
        $result = CrontabLogic::detail($params);
        return $this->data($result);
    }


    /**
     * 编辑定时任务
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function edit()
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
     * @return \think\response\Json
     * @author 段誉
     * @date 2022/3/29 14:27
     */
    /**
     * 删除定时任务
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function delete()
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
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function operate()
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
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function expression()
    {
        $params = (new CrontabValidate())->goCheck('expression');
        $result = CrontabLogic::expression($params);
        return $this->data($result);
    }
}