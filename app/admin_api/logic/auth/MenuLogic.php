<?php
declare(strict_types=1);

namespace app\admin_api\logic\auth;


use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\auth\Admin;
use app\common\model\auth\SystemMenu;
use app\common\model\auth\SystemRoleMenu;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\model\contract\Modelable;


/**
 * 系统菜单
 * @class MenuLogic
 * @package app\admin_api\logic\auth
 * @author LZH
 * @date 2025/2/19
 */
class MenuLogic extends BaseLogic
{

    /**
     * 获取管理员对应的角色菜单
     * @param int $adminId
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function getMenuByAdminId(int $adminId): mixed
    {
        $admin = Admin::findOrEmpty($adminId);

        $where = [];
        $where[] = ['type', 'in', ['M', 'C']];
        $where[] = ['is_disable', '=', 0];

        if ($admin['root'] != 1) {
            $roleMenu = SystemRoleMenu::whereIn('role_id', $admin['role_id'])->column('menu_id');
            $where[] = ['id', 'in', $roleMenu];
        }

        $menu = SystemMenu::where($where)
            ->order(['sort' => 'desc', 'id' => 'asc'])
            ->select();

        return linear_to_tree($menu, 'children');
    }

    /**
     * 添加菜单
     * @param array $params
     * @return SystemMenu|Modelable
     * @author LZH
     * @date 2025/2/19
     */
    public static function add(array $params): Modelable|SystemMenu
    {
        return SystemMenu::create([
            'pid' => $params['pid'],
            'type' => $params['type'],
            'name' => $params['name'],
            'icon' => $params['icon'] ?? '',
            'sort' => $params['sort'],
            'perms' => $params['perms'] ?? '',
            'paths' => $params['paths'] ?? '',
            'component' => $params['component'] ?? '',
            'selected' => $params['selected'] ?? '',
            'params' => $params['params'] ?? '',
            'is_cache' => $params['is_cache'],
            'is_show' => $params['is_show'],
            'is_disable' => $params['is_disable'],
        ]);
    }

    /**
     * 编辑菜单
     * @param array $params
     * @return SystemMenu|Modelable
     * @author LZH
     * @date 2025/2/19
     */
    public static function edit(array $params): Modelable|SystemMenu
    {
        return SystemMenu::update([
            'id' => $params['id'],
            'pid' => $params['pid'],
            'type' => $params['type'],
            'name' => $params['name'],
            'icon' => $params['icon'] ?? '',
            'sort' => $params['sort'],
            'perms' => $params['perms'] ?? '',
            'paths' => $params['paths'] ?? '',
            'component' => $params['component'] ?? '',
            'selected' => $params['selected'] ?? '',
            'params' => $params['params'] ?? '',
            'is_cache' => $params['is_cache'],
            'is_show' => $params['is_show'],
            'is_disable' => $params['is_disable'],
        ]);
    }


    /**
     * 详情
     * @param array $params
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function detail(array $params): array
    {
        return SystemMenu::findOrEmpty($params['id'])->toArray();
    }

    /**
     * 删除菜单
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function delete(array $params): void
    {
        // 删除菜单
        SystemMenu::destroy($params['id']);
        // 删除角色-菜单表中 与该菜单关联的记录
        SystemRoleMenu::where(['menu_id' => $params['id']])->delete();
    }

    /**
     * 更新状态
     * @param array $params
     * @return SystemMenu|Modelable
     * @author LZH
     * @date 2025/2/19
     */
    public static function updateStatus(array $params): Modelable|SystemMenu
    {
        return SystemMenu::update([
            'id' => $params['id'],
            'is_disable' => $params['is_disable']
        ]);
    }

    /**
     * 全部数据
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function getAllData(): mixed
    {
        $data = SystemMenu::where(['is_disable' => YesNoEnum::NO])
            ->field('id,pid,name')
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();

        return linear_to_tree($data, 'children');
    }

}