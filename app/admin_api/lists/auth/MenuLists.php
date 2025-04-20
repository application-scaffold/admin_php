<?php
declare(strict_types=1);

namespace app\admin_api\lists\auth;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\model\auth\SystemMenu;


/**
 * 菜单列表
 * @class MenuLists
 * @package app\admin_api\lists\auth
 * @author LZH
 * @date 2025/2/19
 */
class MenuLists extends BaseAdminDataLists
{

    /**
     * 获取菜单列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $lists = SystemMenu::order(['sort' => 'desc', 'id' => 'asc'])
            ->select()
            ->toArray();
        return linear_to_tree($lists, 'children');
    }


    /**
     * 获取菜单数量
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        return SystemMenu::count();
    }

}