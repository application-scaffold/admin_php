<?php

namespace app\common\model;

use app\common\enum\CrontabEnum;
use think\model\concern\SoftDelete;

/**
 * 定时任务模型
 * @class Crontab
 * @package app\common\model
 * @author LZH
 * @date 2025/2/18
 */
class Crontab extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    protected $name = 'dev_crontab';


    /**
     * 类型获取器
     * @param $value
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getTypeDescAttr($value)
    {
        $desc = [
            CrontabEnum::CRONTAB => '定时任务',
            CrontabEnum::DAEMON => '守护进程',
        ];
        return $desc[$value] ?? '';
    }


    /**
     * 状态获取器
     * @param $value
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getStatusDescAttr($value)
    {
        $desc = [
            CrontabEnum::START => '运行',
            CrontabEnum::STOP => '停止',
            CrontabEnum::ERROR => '错误',
        ];
        return $desc[$value] ?? '';
    }


    /**
     * 最后执行时间获取器
     * @param $value
     * @return false|string
     * @author LZH
     * @date 2025/2/18
     */
    public function getLastTimeAttr($value)
    {
        return empty($value) ? '' : date('Y-m-d H:i:s', $value);
    }
}