<?php


namespace app\admin_api\logic\auth;

use app\common\model\auth\Admin;
use app\common\model\auth\AdminRole;
use app\common\model\auth\SystemMenu;
use app\common\model\auth\SystemRoleMenu;


/**
 * 权限功能类
 * @class AuthLogic
 * @package app\admin_api\logic\auth
 * @author LZH
 * @date 2025/2/19
 */
class AuthLogic
{

    /**
     * 获取全部权限
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public static function getAllAuth()
    {
        return SystemMenu::distinct(true)
            ->where([
                ['is_disable', '=', 0],
                ['perms', '<>', '']
            ])
            ->column('perms');
    }

    /**
     * 获取当前管理员角色按钮权限
     * @param $admin
     * @return string[]
     * @author LZH
     * @date 2025/2/19
     */
    public static function getBtnAuthByRoleId($admin)
    {
        if ($admin['root']) {
            return ['*'];
        }

        $menuId = SystemRoleMenu::whereIn('role_id', $admin['role_id'])
            ->column('menu_id');

        $where[] = ['is_disable', '=', 0];
        $where[] = ['perms', '<>', ''];

        $roleAuth = SystemMenu::distinct(true)
            ->where('id', 'in', $menuId)
            ->where($where)
            ->column('perms');

        $allAuth = SystemMenu::distinct(true)
            ->where($where)
            ->column('perms');

        $hasAllAuth = array_diff($allAuth, $roleAuth);
        if (empty($hasAllAuth)) {
            return ['*'];
        }

        return $roleAuth;
    }


    /**
     * 获取管理员角色关联的菜单id(菜单，权限)
     * @param int $adminId
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getAuthByAdminId(int $adminId): array
    {
        $roleIds = AdminRole::where('admin_id', $adminId)->column('role_id');
        $menuId = SystemRoleMenu::whereIn('role_id', $roleIds)->column('menu_id');

        return SystemMenu::distinct(true)
            ->where([
                ['is_disable', '=', 0],
                ['perms', '<>', ''],
                ['id', 'in', array_unique($menuId)],
            ])
            ->column('perms');
    }
}