<?php
declare (strict_types = 1);

namespace app\common\model\file;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class File extends BaseModel
{
    use SoftDelete;
    protected string $deleteTime = 'delete_time';
}