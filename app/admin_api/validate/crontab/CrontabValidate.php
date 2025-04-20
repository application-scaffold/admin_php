<?php

namespace app\admin_api\validate\crontab;

use app\common\validate\BaseValidate;
use Cron\CronExpression;

/**
 * 定时任务验证器
 * @class CrontabValidate
 * @package app\admin_api\validate\crontab
 * @author LZH
 * @date 2025/2/19
 */
class CrontabValidate extends BaseValidate
{
    protected $rule = [
        'name' => 'require',
        'type' => 'require|in:1',
        'command' => 'require',
        'status' => 'require|in:1,2,3',
        'expression' => 'require|checkExpression',
        'id' => 'require',
        'operate' => 'require'
    ];

    protected $message = [
        'name.require' => '请输入定时任务名称',
        'type.require' => '请选择类型',
        'type.in' => '类型值错误',
        'command.require' => '请输入命令',
        'status.require' => '请选择状态',
        'status.in' => '状态值错误',
        'expression.require' => '请输入运行规则',
        'id.require' => '参数缺失',
        'operate.require' => '请选择操作',
    ];


    /**
     * 添加定时任务场景
     * @return CrontabValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneAdd()
    {
        return $this->remove('id', 'require')->remove('operate', 'require');
    }

    /**
     * 查看定时任务详情场景
     * @return CrontabValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }


    /**
     * 编辑定时任务
     * @return CrontabValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneEdit()
    {
        return $this->remove('operate', 'require');
    }

    /**
     * 删除定时任务场景
     * @return CrontabValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }


    /**
     * @return CrontabValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneOperate()
    {
        return $this->only(['id', 'operate']);
    }

    /**
     * 获取规则执行时间场景
     * @return CrontabValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneExpression()
    {
        return $this->only(['expression']);
    }

    /**
     * 校验运行规则
     * @param $value
     * @param $rule
     * @param $data
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    public function checkExpression($value, $rule, $data)
    {
        if (CronExpression::isValidExpression($value) === false) {
            return '定时任务运行规则错误';
        }
        return true;
    }
}