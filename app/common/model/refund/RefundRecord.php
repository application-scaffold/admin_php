<?php
declare (strict_types = 1);

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
     * @param mixed $value
     * @param array $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getRefundTypeTextAttr(mixed $value, array $data): array|string
    {
        return RefundEnum::getTypeDesc($data['refund_type']);
    }


    /**
     * 退款状态描述
     * @param mixed $value
     * @param array $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getRefundStatusTextAttr(mixed $value, array $data): array|string
    {
        return RefundEnum::getStatusDesc($data['refund_status']);
    }


    /**
     * 退款方式描述
     * @param mixed $value
     * @param array $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getRefundWayTextAttr(mixed $value, array $data): array|string
    {
        return RefundEnum::getWayDesc($data['refund_way']);
    }

}
