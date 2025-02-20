<?php

namespace app\admin_api\logic\auth;

use app\common\{
    cache\AdminAuthCache,
    model\auth\SystemRole,
    logic\BaseLogic,
    model\auth\SystemRoleMenu
};
use think\facade\Db;


/**
 * 角色逻辑层
 * @class RoleLogic
 * @package app\admin_api\logic\auth
 * @author LZH
 * @date 2025/2/19
 */
class RoleLogic extends BaseLogic
{

    /**
     * 添加角色
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            $menuId = !empty($params['menu_id']) ? $params['menu_id'] : [];

            $role = SystemRole::create([
                'name' => $params['name'],
                'desc' => $params['desc'] ?? '',
                'sort' => $params['sort'] ?? 0,
            ]);

            $data = [];
            foreach ($menuId as $item) {
                if (empty($item)) {
                    continue;
                }
                $data[] = [
                    'role_id' => $role['id'],
                    'menu_id' => $item,
                ];
            }
            (new SystemRoleMenu)->insertAll($data);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑角色
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            $menuId = !empty($params['menu_id']) ? $params['menu_id'] : [];

            SystemRole::update([
                'id' => $params['id'],
                'name' => $params['name'],
                'desc' => $params['desc'] ?? '',
                'sort' => $params['sort'] ?? 0,
            ]);

            if (!empty($menuId)) {
                SystemRoleMenu::where(['role_id' => $params['id']])->delete();
                $data = [];
                foreach ($menuId as $item) {
                    $data[] = [
                        'role_id' => $params['id'],
                        'menu_id' => $item,
                    ];
                }
                (new SystemRoleMenu)->insertAll($data);
            }

            (new AdminAuthCache())->deleteTag();

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除角色
     * @param int $id
     * @return true
     * @author LZH
     * @date 2025/2/19
     */
    public static function delete(int $id)
    {
        SystemRole::destroy(['id' => $id]);
        (new AdminAuthCache())->deleteTag();
        return true;
    }

    /**
     * 角色详情
     * @param int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function detail(int $id): array
    {
        $detail = SystemRole::field('id,name,desc,sort')->find($id);
        $authList = $detail->roleMenuIndex()->select()->toArray();
        $menuId = array_column($authList, 'menu_id');
        $detail['menu_id'] = $menuId;
        return $detail->toArray();
    }


    /**
     * 角色数据
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function getAllData()
    {
        return SystemRole::order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();
    }

}