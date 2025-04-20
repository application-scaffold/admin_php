<?php
declare(strict_types=1);

namespace app\admin_api\controller\finance;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\finance\RefundLogLists;
use app\admin_api\lists\finance\RefundRecordLists;
use app\admin_api\logic\finance\RefundLogic;
use think\response\Json;

/**
 * 退款控制器
 * @class RefundController
 * @package app\admin_api\controller\finance
 * @author LZH
 * @date 2025/2/20
 */
class RefundController extends BaseAdminApiController
{

    /**
     * 退还统计
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function stat(): Json
    {
        $result = RefundLogic::stat();
        return $this->success('', $result);
    }


    /**
     * 退款记录
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function record(): Json
    {
        return $this->dataLists(new RefundRecordLists());
    }


    /**
     * 退款日志
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function log(): Json
    {
        $recordId = $this->request->get('record_id', 0);
        $result = RefundLogic::refundLog($recordId);
        return $this->success('', $result);
    }

}