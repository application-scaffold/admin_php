<?php
declare (strict_types = 1);

namespace app\common\model\user;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 账户流水记录模型
 * @class UserAccountLog
 * @package app\common\model\user
 * @author LZH
 * @date 2025/2/18
 */
class UserAccountLog extends BaseModel
{
    use SoftDelete;

    protected string $deleteTime = 'delete_time';
}