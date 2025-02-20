<?php

namespace app\front_api\lists\recharge;

use app\front_api\lists\BaseApiDataLists;
use app\common\enum\PayEnum;
use app\common\model\recharge\RechargeOrder;

/**
 * 充值记录列表
 * @class RechargeLists
 * @package app\front_api\lists\recharge
 * @author LZH
 * @date 2025/2/19
 */
class RechargeLists extends BaseApiDataLists
{
    /**
     * 获取列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $lists = RechargeOrder::field('order_amount,create_time')
            ->where([
                'user_id' => $this->userId,
                'pay_status' => PayEnum::ISPAID
            ])
            ->order('id', 'desc')
            ->select()
            ->toArray();

        foreach($lists as &$item) {
            $item['tips'] = '充值' . format_amount($item['order_amount']) . '元';
        }

        return $lists;
    }


    /**
     * 获取数量
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        return RechargeOrder::where([
                'user_id' => $this->userId,
                'pay_status' => PayEnum::ISPAID
            ])
            ->count();
    }

}