<?php

namespace app\common\model\auth;

use app\common\model\BaseModel;

class AdminJobs extends BaseModel
{

    /**
     * 删除用户关联岗位
     * @param $adminId
     * @return bool
     * @author LZH
     * @date 2025/2/18
     */
    public static function delByUserId($adminId)
    {
        return self::where(['admin_id' => $adminId])->delete();
    }
}