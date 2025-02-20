<?php

namespace app\common\model\refund;

use app\common\enum\RefundEnum;
use app\common\model\auth\Admin;
use app\common\model\BaseModel;

/**
 * 退款日志模型
 * @class RefundLog
 * @package app\common\model\refund
 * @author LZH
 * @date 2025/2/18
 */
class RefundLog extends BaseModel
{

    /**
     * 操作人描述
     * @param $value
     * @param $data
     * @return mixed
     * @author LZH
     * @date 2025/2/18
     */
    public function getHandlerAttr($value, $data)
    {
        return Admin::where('id', $data['handle_id'])->value('name');
    }


    /**
     * 退款状态描述
     * @param $value
     * @param $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getRefundStatusTextAttr($value, $data)
    {
        return RefundEnum::getStatusDesc($data['refund_status']);
    }

}
