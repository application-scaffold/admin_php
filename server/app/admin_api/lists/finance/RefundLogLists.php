<?php

namespace app\admin_api\lists\finance;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\model\refund\RefundLog;

/**
 * 退款日志列表
 * @class RefundLogLists
 * @package app\admin_api\lists\finance
 * @author LZH
 * @date 2025/2/19
 */
class RefundLogLists extends BaseAdminDataLists
{

    /**
     * 查询条件
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function queryWhere()
    {
        $where[] = ['record_id', '=', $this->params['record_id'] ?? 0];
        return $where;
    }

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
        $lists = (new RefundLog())
            ->order(['id' => 'desc'])
            ->where($this->queryWhere())
            ->limit($this->limitOffset, $this->limitLength)
            ->hidden(['refund_msg'])
            ->append(['handler', 'refund_status_text'])
            ->select()
            ->toArray();
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
        return (new RefundLog())
            ->where($this->queryWhere())
            ->count();
    }

}
