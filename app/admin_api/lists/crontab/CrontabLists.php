<?php
declare(strict_types=1);

namespace app\admin_api\lists\crontab;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\model\Crontab;


/**
 * 定时任务列表
 * @class CrontabLists
 * @package app\admin_api\lists\crontab
 * @author LZH
 * @date 2025/2/19
 */
class CrontabLists extends BaseAdminDataLists
{

    /**
     * 定时任务列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $field = 'id,name,type,type as type_desc,command,params,expression,
        status,status as status_desc,error,last_time,time,max_time';

        $lists = Crontab::field($field)
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id', 'desc')
            ->select()
            ->toArray();

        return $lists;
    }


    /**
     * 定时任务数量
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        return Crontab::count();
    }
}