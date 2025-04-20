<?php

namespace app\common\model\auth;

use app\common\model\BaseModel;

class AdminSession extends BaseModel
{
    /**
     * 关联管理员表
     * @return \think\model\relation\HasOne
     * @author LZH
     * @date 2025/2/18
     */
    public function admin()
    {
        return $this->hasOne(Admin::class, 'id', 'admin_id')
            ->field('id,multipoint_login');
    }
}