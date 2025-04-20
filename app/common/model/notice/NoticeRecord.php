<?php
declare (strict_types = 1);

namespace app\common\model\notice;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 通知记录模型
 * @class NoticeRecord
 * @package app\common\model\notice
 * @author LZH
 * @date 2025/2/18
 */
class NoticeRecord extends BaseModel
{
    use SoftDelete;

    protected string $deleteTime = 'delete_time';

}