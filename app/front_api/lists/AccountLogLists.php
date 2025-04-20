<?php
declare(strict_types=1);

namespace app\front_api\lists;

use app\common\enum\user\AccountLogEnum;
use app\common\model\user\UserAccountLog;

/**
 * 账户流水列表
 * @class AccountLogLists
 * @package app\front_api\lists
 * @author LZH
 * @date 2025/2/19
 */
class AccountLogLists extends BaseApiDataLists
{
    /**
     * 搜索条件
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function queryWhere(): array
    {
        // 指定用户
        $where[] = ['user_id', '=', $this->userId];

        // 用户月明细
        if (isset($this->params['type']) && $this->params['type'] == 'um') {
            $where[] = ['change_type', 'in', AccountLogEnum::getUserMoneyChangeType()];
        }

        // 变动类型
        if (!empty($this->params['action'])) {
            $where[] = ['action', '=', $this->params['action']];
        }

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
        $field = 'change_type,change_amount,action,create_time,remark';
        $lists = UserAccountLog::field($field)
            ->where($this->queryWhere())
            ->order('id', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['type_desc'] = AccountLogEnum::getChangeTypeDesc($item['change_type']);
            $symbol = $item['action'] == AccountLogEnum::DEC ? '-' : '+';
            $item['change_amount_desc'] = $symbol . $item['change_amount'];
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
        return UserAccountLog::where($this->queryWhere())->count();
    }
}