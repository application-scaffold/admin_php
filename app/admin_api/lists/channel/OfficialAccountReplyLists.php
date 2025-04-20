<?php

namespace app\admin_api\lists\channel;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\channel\OfficialAccountReply;

/**
 * 微信公众号回复列表
 * @class OfficialAccountReplyLists
 * @package app\admin_api\lists\channel
 * @author LZH
 * @date 2025/2/19
 */
class OfficialAccountReplyLists extends BaseAdminDataLists implements ListsSearchInterface
{

    /**
     * 设置搜索
     * @return array[]
     * @author LZH
     * @date 2025/2/19
     */
    public function setSearch(): array
    {
        return [
            '=' => ['reply_type']
        ];
    }


    /**
     * 回复列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $field = 'id,name,keyword,matching_type,content,content_type,status,sort';
        $field .= ',matching_type as matching_type_desc,content_type as content_type_desc,status as status_desc';

        $lists = OfficialAccountReply::field($field)
            ->where($this->searchWhere)
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        return $lists;
    }


    /**
     * 回复记录数
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        $count = OfficialAccountReply::where($this->searchWhere)->count();

        return $count;
    }
}