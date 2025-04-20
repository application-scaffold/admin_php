<?php
declare(strict_types=1);

namespace app\admin_api\logic\finance;

use app\common\enum\RefundEnum;
use app\common\logic\BaseLogic;
use app\common\model\refund\RefundLog;
use app\common\model\refund\RefundRecord;


/**
 * 退款
 * @class RefundLogic
 * @package app\admin_api\logic\finance
 * @author LZH
 * @date 2025/2/19
 */
class RefundLogic extends BaseLogic
{

    /**
     * 退款统计
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function stat(): array
    {
        $records = RefundRecord::select()->toArray();

        $total = 0;
        $ing = 0;
        $success = 0;
        $error = 0;

        foreach ($records as $record) {
            $total += $record['order_amount'];
            switch ($record['refund_status']) {
                case RefundEnum::REFUND_ING:
                    $ing += $record['order_amount'];
                    break;
                case RefundEnum::REFUND_SUCCESS:
                    $success += $record['order_amount'];
                    break;
                case RefundEnum::REFUND_ERROR:
                    $error += $record['order_amount'];
                    break;
            }
        }

        return [
            'total' => round($total, 2),
            'ing' => round($ing, 2),
            'success' => round($success, 2),
            'error' => round($error, 2),
        ];
    }

    /**
     * 退款日志
     * @param $recordId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function refundLog(string $recordId): array
    {
        return (new RefundLog())
            ->order(['id' => 'desc'])
            ->where('record_id', $recordId)
            ->hidden(['refund_msg'])
            ->append(['handler', 'refund_status_text'])
            ->select()
            ->toArray();
    }

}