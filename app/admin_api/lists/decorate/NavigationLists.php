<?php
declare(strict_types=1);

namespace app\admin_api\lists\decorate;


use app\admin_api\lists\BaseAdminDataLists;
use app\common\model\decorate\Navigation;

/**
 * 底部导航列表
 * Class NavigationLists
 * @package app\admin_api\lists\decorate
 */
class NavigationLists extends BaseAdminDataLists
{
    /**
     * @notes 底部导航列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2022/2/14 10:12 上午
     */
    public function lists(): array
    {
        // TODO
        return (new Navigation())->select()->toArray();
    }

    /**
     * @notes 底部导航总数
     * @return int
     * @author ljj
     * @date 2022/2/14 10:13 上午
     */
    public function count(): int
    {
        return (new Navigation())->count();
    }
}