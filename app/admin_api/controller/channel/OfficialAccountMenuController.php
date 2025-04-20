<?php

namespace app\admin_api\controller\channel;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\channel\OfficialAccountMenuLogic;

/**
 * 微信公众号菜单控制器
 * @class OfficialAccountMenuController
 * @package app\admin_api\controller\channel
 * @author LZH
 * @date 2025/2/20
 */
class OfficialAccountMenuController extends BaseAdminApiController
{

    /**
     * 保存菜单
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function save()
    {
        $params = $this->request->post();
        $result = OfficialAccountMenuLogic::save($params);
        if(false === $result) {
            return $this->fail(OfficialAccountMenuLogic::getError());
        }
        return $this->success('保存成功',[],1,1);
    }


    /**
     * 保存发布菜单
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function saveAndPublish()
    {
        $params = $this->request->post();
        $result = OfficialAccountMenuLogic::saveAndPublish($params);
        if($result) {
            return $this->success('保存并发布成功',[],1,1);
        }
        return $this->fail(OfficialAccountMenuLogic::getError());
    }


    /**
     * 查看菜单详情
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail()
    {
        $result = OfficialAccountMenuLogic::detail();
        return $this->data($result);
    }
}