<?php
declare(strict_types=1);

namespace app\admin_api\controller\recharge;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\recharge\RechargeLists;
use app\admin_api\logic\recharge\RechargeLogic;
use app\admin_api\validate\recharge\RechargeRefundValidate;
use think\response\Json;

/**
 * 充值控制器
 * @class RechargeController
 * @package app\admin_api\controller\recharge
 * @author LZH
 * @date 2025/2/20
 */
class RechargeController extends BaseAdminApiController
{

    /**
     * 获取充值设置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getConfig(): Json
    {
        $result = RechargeLogic::getConfig();
        return $this->data($result);
    }


    /**
     * 充值设置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setConfig(): Json
    {
        $params = $this->request->post();
        $result = RechargeLogic::setConfig($params);
        if($result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(RechargeLogic::getError());
    }

    /**
     * 充值记录
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists(): Json
    {
        return $this->dataLists(new RechargeLists());
    }


    /**
     * 退款
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function refund(): Json
    {
        $params = (new RechargeRefundValidate())->post()->goCheck('refund');
        $result = RechargeLogic::refund($params, $this->adminId);
        list($flag, $msg) = $result;
        if(false === $flag) {
            return $this->fail($msg);
        }
        return $this->success($msg, [], 1, 1);
    }

    /**
     * 重新退款
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function refundAgain(): Json
    {
        $params = (new RechargeRefundValidate())->post()->goCheck('again');
        $result = RechargeLogic::refundAgain($params, $this->adminId);
        list($flag, $msg) = $result;
        if(false === $flag) {
            return $this->fail($msg);
        }
        return $this->success($msg, [], 1, 1);
    }

}