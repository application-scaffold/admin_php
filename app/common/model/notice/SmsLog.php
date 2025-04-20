<?php

namespace app\common\model\notice;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 短信记录模型
 * @class SmsLog
 * @package app\common\model\notice
 * @author LZH
 * @date 2025/2/18
 */
class SmsLog extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';
}