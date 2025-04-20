<?php
declare (strict_types = 1);

namespace app\common\model\auth;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use think\model\relation\HasMany;

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

    protected string $deleteTime = 'delete_time';

    protected $name = 'system_role';

    /**
     * 角色与菜单关联关系
     * @return HasMany
     * @author LZH
     * @date 2025/2/18
     */
    public function roleMenuIndex(): HasMany
    {
        return $this->hasMany(SystemRoleMenu::class, 'role_id');
    }
}