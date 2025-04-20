<?php
declare(strict_types=1);

namespace app\admin_api\lists\finance;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\enum\user\AccountLogEnum;
use app\common\lists\ListsSearchInterface;
use app\common\model\user\UserAccountLog;
use app\common\service\FileService;


/**
 * 账记流水列表
 * @class AccountLogLists
 * @package app\admin_api\lists\finance
 * @author LZH
 * @date 2025/2/19
 */
class AccountLogLists extends BaseAdminDataLists implements ListsSearchInterface
{

    /**
     * 搜索条件
     * @return array[]
     * @author LZH
     * @date 2025/2/19
     */
    public function setSearch(): array
    {
        return [
            '=' => ['al.change_type'],
        ];
    }

    /**
     * 搜索条件
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function queryWhere(): array
    {
        $where = [];
        // 用户余额
        if (isset($this->params['type']) && $this->params['type'] == 'um') {
            $where[] = ['change_type', 'in', AccountLogEnum::getUserMoneyChangeType()];
        }

        if (!empty($this->params['user_info'])) {
            $where[] = ['u.sn|u.nickname|u.mobile|u.account', 'like', '%' . $this->params['user_info'] . '%'];
        }

        if (!empty($this->params['start_time'])) {
            $where[] = ['al.create_time', '>=', strtotime($this->params['start_time'])];
        }

        if (!empty($this->params['end_time'])) {
            $where[] = ['al.create_time', '<=', strtotime($this->params['end_time'])];
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
        $field = 'u.nickname,u.account,u.sn,u.avatar,u.mobile,al.action,al.change_amount,al.left_amount,al.change_type,al.source_sn,al.create_time';
        $lists = UserAccountLog::alias('al')
            ->join('user u', 'u.id = al.user_id')
            ->field($field)
            ->where($this->searchWhere)
            ->where($this->queryWhere())
            ->order('al.id', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['avatar'] = FileService::getFileUrl($item['avatar']);
            $item['change_type_desc'] = AccountLogEnum::getChangeTypeDesc($item['change_type']);
            $symbol = $item['action'] == AccountLogEnum::INC ? '+' : '-';
            $item['change_amount'] = $symbol . $item['change_amount'];
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
        return UserAccountLog::alias('al')
            ->join('user u', 'u.id = al.user_id')
            ->where($this->queryWhere())
            ->where($this->searchWhere)
            ->count();
    }
}