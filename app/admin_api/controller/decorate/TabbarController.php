<?php

namespace app\admin_api\controller\decorate;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\decorate\DecorateTabbarLogic;

/**
 * 装修-底部导航
 * @class TabbarController
 * @package app\admin_api\controller\decorate
 * @author LZH
 * @date 2025/2/20
 */
class TabbarController extends BaseAdminApiController
{

    /**
     * 底部导航详情
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function detail()
    {
        $data = DecorateTabbarLogic::detail();
        return $this->success('', $data);
    }

    /**
     * 底部导航保存
     * @return \think\response\Json
     * @throws \Exception
     * @author LZH
     * @date 2025/2/20
     */
    public function save()
    {
        $params = $this->request->post();
        DecorateTabbarLogic::save($params);
        return $this->success('操作成功', [], 1, 1);
    }

}