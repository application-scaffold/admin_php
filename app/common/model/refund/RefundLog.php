<?php
declare (strict_types = 1);

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
     * @param mixed $value
     * @param array $data
     * @return mixed
     * @author LZH
     * @date 2025/2/18
     */
    public function getHandlerAttr(mixed $value, array $data): mixed
    {
        return Admin::where('id', $data['handle_id'])->value('name');
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

}
