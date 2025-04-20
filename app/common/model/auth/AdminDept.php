<?php
declare (strict_types = 1);

namespace app\common\model\auth;

use app\common\model\BaseModel;

class AdminDept extends BaseModel
{

    /**
     * 删除用户关联部门
     * @param string $adminId
     * @return bool
     * @author LZH
     * @date 2025/2/18
     */
    public static function delByUserId(string $adminId): bool
    {
        return self::where(['admin_id' => $adminId])->delete();
    }
}