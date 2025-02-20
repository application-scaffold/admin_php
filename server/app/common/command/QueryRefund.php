<?php

namespace app\common\command;

use app\common\enum\PayEnum;
use app\common\enum\RefundEnum;
use app\common\model\recharge\RechargeOrder;
use app\common\model\refund\RefundLog;
use app\common\model\refund\RefundRecord;
use app\common\service\pay\WeChatPayService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

/**
 * 退款状态查询命令
 * @class QueryRefund
 * @package app\common\command
 * @author LZH
 * @date 2025/2/18
 */
class QueryRefund extends Command
{
    /**
     * 配置命令名称和描述
     */
    protected function configure()
    {
        $this->setName('query_refund') // 设置命令名称为 'query_refund'
            ->setDescription('订单退款状态处理'); // 设置命令描述为 '订单退款状态处理'
    }

    /**
     * 执行退款状态查询
     * @param Input $input 输入对象
     * @param Output $output 输出对象
     * @return bool 如果没有退款记录，返回false
     */
    protected function execute(Input $input, Output $output)
    {
        try {
            // 查找退款中的退款记录（微信、支付宝支付）
            $refundRecords = (new RefundLog())->alias('l')
                ->join('refund_record r', 'r.id = l.record_id')
                ->field([
                    'l.id' => 'log_id', 'l.sn' => 'log_sn',
                    'r.id' => 'record_id', 'r.order_id', 'r.sn' => 'record_sn', 'r.order_type'
                ])
                ->where(['l.refund_status' => RefundEnum::REFUND_ING])
                ->select()->toArray();

            if (empty($refundRecords)) {
                return false; // 如果没有退款记录，直接返回false
            }

            // 分别处理各个类型订单
            $rechargeRecords = array_filter($refundRecords, function ($item) {
                return $item['order_type'] == RefundEnum::ORDER_TYPE_RECHARGE;
            });

            if (!empty($rechargeRecords)) {
                // 处理充值订单的退款状态
                $this->handleRechargeOrder($rechargeRecords);
            }

            return true;
        } catch (\Exception $e) {
            // 捕获异常，记录错误日志
            Log::write('订单退款状态查询失败,失败原因:' . $e->getMessage());
            return false;
        }
    }

    /**
     * 处理充值订单的退款状态
     * @param array $refundRecords 退款记录列表
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function handleRechargeOrder($refundRecords)
    {
        // 获取所有退款记录对应的订单ID
        $orderIds = array_unique(array_column($refundRecords, 'order_id'));
        // 查询这些订单的详细信息
        $Orders = RechargeOrder::whereIn('id', $orderIds)->column('*', 'id');

        foreach ($refundRecords as $record) {
            if (!isset($Orders[$record['order_id']])) {
                continue; // 如果订单不存在，跳过当前记录
            }

            $order = $Orders[$record['order_id']];
            if (!in_array($order['pay_way'], [PayEnum::WECHAT_PAY, PayEnum::ALI_PAY])) {
                continue; // 如果支付方式不是微信或支付宝，跳过当前记录
            }

            // 检查退款状态
            $this->checkReFundStatus([
                'record_id' => $record['record_id'],
                'log_id' => $record['log_id'],
                'log_sn' => $record['log_sn'],
                'pay_way' => $order['pay_way'],
                'order_terminal' => $order['order_terminal'],
            ]);
        }
    }

    /**
     * 校验退款状态
     * @param array $refundData 退款数据
     * @return bool 如果退款状态更新成功，返回true
     * @author LZH
     * @date 2025/2/18
     */
    public function checkReFundStatus($refundData)
    {
        $result = null;
        switch ($refundData['pay_way']) {
            case PayEnum::WECHAT_PAY:
                // 查询微信支付的退款状态
                $result = self::checkWechatRefund($refundData['order_terminal'], $refundData['log_sn']);
                break;
        }

        if (is_null($result)) {
            return false; // 如果退款状态查询失败，返回false
        }

        if (true === $result) {
            // 如果退款成功，更新退款记录为成功状态
            $this->updateRefundSuccess($refundData['log_id'], $refundData['record_id']);
        } else {
            // 如果退款失败，更新退款记录的错误信息
            $this->updateRefundMsg($refundData['log_id'], $result);
        }
        return true;
    }

    /**
     * 查询微信支付的退款状态
     * @param string $orderTerminal 订单终端
     * @param string $refundLogSn 退款日志编号
     * @return string|true|null 如果退款成功返回true，失败返回错误信息，查询失败返回null
     * @author LZH
     * @date 2025/2/18
     */
    public function checkWechatRefund($orderTerminal, $refundLogSn)
    {
        // 根据商户退款单号查询退款状态
        $result = (new WeChatPayService($orderTerminal))->queryRefund($refundLogSn);

        if (!empty($result['status']) && $result['status'] == 'SUCCESS') {
            return true; // 如果退款状态为成功，返回true
        }

        if (!empty($result['code']) || !empty($result['message'])) {
            // 如果有错误信息，返回错误信息
            return '微信:' . $result['code'] . '-' . $result['message'];
        }

        return null; // 如果查询失败，返回null
    }

    /**
     * 更新退款记录为成功状态
     * @param int $logId 退款日志ID
     * @param int $recordId 退款记录ID
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function updateRefundSuccess($logId, $recordId)
    {
        // 更新退款日志为成功状态
        RefundLog::update([
            'id' => $logId,
            'refund_status' => RefundEnum::REFUND_SUCCESS,
        ]);
        // 更新退款记录为成功状态
        RefundRecord::update([
            'id' => $recordId,
            'refund_status' => RefundEnum::REFUND_SUCCESS,
        ]);
    }

    /**
     * 更新退款记录的错误信息
     * @param int $logId 退款日志ID
     * @param string $msg 错误信息
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function updateRefundMsg($logId, $msg)
    {
        // 更新退款日志的错误信息
        RefundLog::update([
            'id' => $logId,
            'refund_msg' => $msg,
        ]);
    }
}
