<?php
declare (strict_types = 1);

namespace app\common\model\auth;

use app\common\model\BaseModel;
use think\model\relation\HasOne;

class AdminSession extends BaseModel
{
    /**
     * 关联管理员表
     * @return HasOne
     * @author LZH
     * @date 2025/2/18
     */
    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class, 'id', 'admin_id')
            ->field('id,multipoint_login');
    }
}