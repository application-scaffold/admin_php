<?php

namespace app\admin_api\lists\auth;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\model\auth\AdminRole;
use app\common\model\auth\SystemRole;


/**
 * 角色列表
 * @class RoleLists
 * @package app\admin_api\lists\auth
 * @author LZH
 * @date 2025/2/19
 */
class RoleLists extends BaseAdminDataLists
{

    /**
     * 导出字段
     * @return string[]
     * @author LZH
     * @date 2025/2/19
     */
    public function setExcelFields(): array
    {
        return [
            'name' => '角色名称',
            'desc' => '备注',
            'create_time' => '创建时间'
        ];
    }

    /**
     * 导出表名
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function setFileName(): string
    {
        return '角色表';
    }

    /**
     * 角色列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $lists = SystemRole::with(['role_menu_index'])
            ->field('id,name,desc,sort,create_time')
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();

        foreach ($lists as $key => $role) {
            //使用角色的人数
            $lists[$key]['num'] = AdminRole::where('role_id', $role['id'])->count();
            $menuId = array_column($role['role_menu_index'], 'menu_id');
            $lists[$key]['menu_id'] = $menuId;
            unset($lists[$key]['role_menu_index']);
        }

        return $lists;
    }

    /**
     * 总记录数
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        return SystemRole::count();
    }
}