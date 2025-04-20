<?php

namespace app\admin_api\lists\decorate;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\enum\MenuEnum;
use app\common\model\decorate\Menu;

/**
 * 菜单列表
 * @class MenuLists
 * @package app\admin_api\lists\decorate
 * @author LZH
 * @date 2025/2/19
 */
class MenuLists extends BaseAdminDataLists
{

    /**
     * 菜单列表
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $lists = (new Menu())->field('id,name,image,link_type,link_address,sort,status')
            ->order(['sort'=>'asc','id'=>'desc'])
            ->append(['link_address_desc','status_desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as &$list) {
            $list['link_address_desc'] = MenuEnum::getLinkDesc($list['link_type']).':'.$list['link_address_desc'];
        }

        return $lists;
    }

    /**
     * 菜单总数
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        return (new Menu())->count();
    }
}