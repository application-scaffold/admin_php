<?php
declare(strict_types=1);

namespace app\admin_api\lists\finance;


use app\admin_api\lists\BaseAdminDataLists;
use app\common\enum\RefundEnum;
use app\common\lists\ListsExtendInterface;
use app\common\lists\ListsSearchInterface;
use app\common\model\refund\RefundRecord;
use app\common\service\FileService;

/**
 * 退款记录列表
 * @class RefundRecordLists
 * @package app\admin_api\lists\finance
 * @author LZH
 * @date 2025/2/19
 */
class RefundRecordLists extends BaseAdminDataLists implements ListsSearchInterface, ListsExtendInterface
{

    /**
     * 查询条件
     * @return array[]
     * @author LZH
     * @date 2025/2/19
     */
    public function setSearch(): array
    {
        return [
            '=' => ['r.sn', 'r.order_sn', 'r.refund_type'],
        ];
    }

    /**
     * 查询条件
     * @param bool $flag
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function queryWhere(bool $flag = true): array
    {
        $where = [];
        if (!empty($this->params['user_info'])) {
            $where[] = ['u.sn|u.nickname|u.mobile|u.account', 'like', '%' . $this->params['user_info'] . '%'];
        }
        if (!empty($this->params['start_time'])) {
            $where[] = ['r.create_time', '>=', strtotime($this->params['start_time'])];
        }
        if (!empty($this->params['end_time'])) {
            $where[] = ['r.create_time', '<=', strtotime($this->params['end_time'])];
        }

        if ($flag == true) {
            if (isset($this->params['refund_status']) && $this->params['refund_status'] != '') {
                $where[] = ['r.refund_status', '=', $this->params['refund_status']];
            }
        }

        return $where;
    }


    /**
     * 获取列表
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $lists = (new RefundRecord())->alias('r')
            ->field('r.*,u.nickname,u.avatar')
            ->join('user u', 'u.id = r.user_id')
            ->order(['r.id' => 'desc'])
            ->where($this->searchWhere)
            ->where($this->queryWhere())
            ->limit($this->limitOffset, $this->limitLength)
            ->append(['refund_type_text', 'refund_status_text', 'refund_way_text'])
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['avatar'] = FileService::getFileUrl($item['avatar']);
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
        return (new RefundRecord())->alias('r')
            ->join('user u', 'u.id = r.user_id')
            ->where($this->searchWhere)
            ->where($this->queryWhere())
            ->count();
    }

    /**
     * 额外参数
     * @return mixed|null
     * @author LZH
     * @date 2025/2/19
     */
    public function extend(): array
    {
        $count = (new RefundRecord())->alias('r')
            ->join('user u', 'u.id = r.user_id')
            ->field([
                'count(r.id) as total',
                'count(if(r.refund_status='.RefundEnum::REFUND_ING.', true, null)) as ing',
                'count(if(r.refund_status='.RefundEnum::REFUND_SUCCESS.', true, null)) as success',
                'count(if(r.refund_status='.RefundEnum::REFUND_ERROR.', true, null)) as error',
            ])
            ->where($this->searchWhere)
            ->where($this->queryWhere(false))
            ->select()->toArray();

        return array_shift($count);
    }
}
