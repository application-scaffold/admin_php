<?php
declare(strict_types=1);

namespace app\common\command;

use app\common\enum\CrontabEnum;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use Cron\CronExpression;
use think\facade\Console;
use app\common\model\Crontab as CrontabModel;

/**
 * 定时任务管理类，该类用于管理和执行定时任务，支持基于Cron表达式的任务调度。
 * @class Crontab
 * @package app\common\command
 * @author LZH
 * @date 2025/2/18
 */
class Crontab extends Command
{
    /**
     * 配置命令名称和描述
     */
    protected function configure(): void
    {
        $this->setName('crontab') // 设置命令名称为 'crontab'
            ->setDescription('定时任务'); // 设置命令描述为 '定时任务'
    }

    /**
     * 执行定时任务
     * @param Input $input 输入对象
     * @param Output $output 输出对象
     * @return bool 如果没有任何任务执行，返回false
     * @throws \Exception
     */
    protected function execute(Input $input, Output $output): bool
    {
        // 获取所有状态为“启动”的定时任务
        $lists = CrontabModel::where('status', CrontabEnum::START)->select()->toArray();
        if (empty($lists)) {
            return false; // 如果没有任务，直接返回false
        }

        $time = time(); // 获取当前时间戳
        foreach ($lists as $item) {
            if (empty($item['last_time'])) {
                // 如果任务从未执行过，计算下一次执行时间并更新到数据库
                $lastTime = (new CronExpression($item['expression']))
                    ->getNextRunDate()
                    ->getTimestamp();
                CrontabModel::where('id', $item['id'])->update([
                    'last_time' => $lastTime,
                ]);
                continue;
            }

            // 计算任务的下一次执行时间
            $nextTime = (new CronExpression($item['expression']))
                ->getNextRunDate($item['last_time'])
                ->getTimestamp();
            if ($nextTime > $time) {
                // 如果未到执行时间，跳过当前任务
                continue;
            }

            // 执行任务
            self::start($item);
        }
        return true;
    }

    /**
     * 执行单个定时任务
     * @param array $item 任务信息
     */
    public static function start(array $item): void
    {
        // 记录任务开始时间
        $startTime = microtime(true);
        try {
            // 解析任务参数
            $params = explode(' ', $item['params']);
            if (is_array($params) && !empty($item['params'])) {
                // 调用控制台命令执行任务，并传入参数
                Console::call($item['command'], $params);
            } else {
                // 调用控制台命令执行任务，不传入参数
                Console::call($item['command']);
            }

            // 清除任务错误信息
            CrontabModel::where('id', $item['id'])->update(['error' => '']);
        } catch (\Exception $e) {
            // 捕获异常，记录错误信息，并将任务状态设置为“错误”
            CrontabModel::where('id', $item['id'])->update([
                'error' => $e->getMessage(),
                'status' => CrontabEnum::ERROR
            ]);
        } finally {
            // 记录任务结束时间
            $endTime = microtime(true);
            // 计算任务执行时间
            $useTime = round(($endTime - $startTime), 2);
            // 更新任务的最大执行时间
            $maxTime = max($useTime, $item['max_time']);
            // 更新任务的最后执行时间、执行时间和最大执行时间
            CrontabModel::where('id', $item['id'])->update([
                'last_time' => time(),
                'time' => $useTime,
                'max_time' => $maxTime
            ]);
        }
    }
}
