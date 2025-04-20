<?php

namespace app\common\model\auth;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 角色模型
 * @class SystemRole
 * @package app\common\model\auth
 * @author LZH
 * @date 2025/2/18
 */
class SystemRole extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    protected $name = 'system_role';

    /**
     * 角色与菜单关联关系
     * @return \think\model\relation\HasMany
     * @author LZH
     * @date 2025/2/18
     */
    public function roleMenuIndex()
    {
        return $this->hasMany(SystemRoleMenu::class, 'role_id');
    }
}