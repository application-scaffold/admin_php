<?php

namespace app\admin_api\controller\notice;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\notice\NoticeSettingLists;
use app\admin_api\logic\notice\NoticeLogic;
use app\admin_api\validate\notice\NoticeValidate;

/**
 * 通知控制器
 * @class NoticeController
 * @package app\admin_api\controller\notice
 * @author LZH
 * @date 2025/2/20
 */
class NoticeController extends BaseAdminApiController
{

    /**
     * 查看通知设置列表
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function settingLists()
    {
        return $this->dataLists(new NoticeSettingLists());
    }


    /**
     * 查看通知设置详情
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail()
    {
        $params = (new NoticeValidate())->goCheck('detail');
        $result = NoticeLogic::detail($params);
        return $this->data($result);
    }


    /**
     * 通知设置
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function set()
    {
        $params = $this->request->post();
        $result = NoticeLogic::set($params);
        if ($result) {
            return $this->success('设置成功');
        }
        return $this->fail(NoticeLogic::getError());
    }
}