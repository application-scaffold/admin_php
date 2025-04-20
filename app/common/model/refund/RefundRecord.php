<?php

namespace app\common\model\refund;

use app\common\enum\RefundEnum;
use app\common\model\BaseModel;

/**
 * 退款记录模型
 * @class RefundRecord
 * @package app\common\model\refund
 * @author LZH
 * @date 2025/2/18
 */
class RefundRecord extends BaseModel
{

    /**
     * 退款类型描述
     * @param $value
     * @param $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getRefundTypeTextAttr($value, $data)
    {
        return RefundEnum::getTypeDesc($data['refund_type']);
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


    /**
     * 退款方式描述
     * @param $value
     * @param $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getRefundWayTextAttr($value, $data)
    {
        return RefundEnum::getWayDesc($data['refund_way']);
    }

}
