<?php

namespace app\admin_api\lists\notice;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\notice\NoticeSetting;

/**
 * 通知设置
 * @class NoticeSettingLists
 * @package app\admin_api\lists\notice
 * @author LZH
 * @date 2025/2/19
 */
class NoticeSettingLists extends BaseAdminDataLists implements ListsSearchInterface
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
            '=' => ['recipient', 'type']
        ];
    }

    /**
     * 通知设置列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $lists = (new NoticeSetting())->field('id,scene_name,sms_notice,type')
            ->append(['sms_status_desc','type_desc'])
            ->where($this->searchWhere)
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * 通知设置数量
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        return (new NoticeSetting())->where($this->searchWhere)->count();
    }
}