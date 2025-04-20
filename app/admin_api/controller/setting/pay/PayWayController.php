<?php

namespace app\admin_api\controller\setting\pay;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\setting\pay\PayWayLogic;

/**
 * 支付方式
 * @class PayWayController
 * @package app\admin_api\controller\setting\pay
 * @author LZH
 * @date 2025/2/20
 */
class PayWayController extends BaseAdminApiController
{

    /**
     * 获取支付方式
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function getPayWay()
    {
        $result = PayWayLogic::getPayWay();
        return $this->success('获取成功',$result);
    }

    /**
     * 设置支付方式
     * @return \think\response\Json
     * @throws \Exception
     * @author LZH
     * @date 2025/2/20
     */
    public function setPayWay()
    {
        $params = $this->request->post();
        $result = (new PayWayLogic())->setPayWay($params);
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('操作成功',[],1, 1);
    }
}