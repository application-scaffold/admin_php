<?php
declare(strict_types=1);

namespace app\admin_api\logic\crontab;

use app\common\enum\CrontabEnum;
use app\common\logic\BaseLogic;
use app\common\model\Crontab;
use Cron\CronExpression;

/**
 * 定时任务逻辑层
 * @class CrontabLogic
 * @package app\admin_api\logic\crontab
 * @author LZH
 * @date 2025/2/19
 */
class CrontabLogic extends BaseLogic
{

    /**
     * 添加定时任务
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function add(array $params): bool
    {
        try {
            $params['remark'] = $params['remark'] ?? '';
            $params['params'] = $params['params'] ?? '';
            $params['last_time'] = time();

            Crontab::create($params);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * 查看定时任务详情
     * @param array $params
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function detail(array $params): array
    {
        $field = 'id,name,type,type as type_desc,command,params,status,status as status_desc,expression,remark';
        $crontab = Crontab::field($field)->findOrEmpty($params['id']);
        if ($crontab->isEmpty()) {
            return [];
        }
        return $crontab->toArray();
    }


    /**
     * 编辑定时任务
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function edit(array $params): bool
    {
        try {
            $params['remark'] = $params['remark'] ?? '';
            $params['params'] = $params['params'] ?? '';

            Crontab::update($params);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * 删除定时任务
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function delete(array $params): bool
    {
        try {
            Crontab::destroy($params['id']);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 操作定时任务
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function operate(array $params): bool
    {
        try {
            $crontab = Crontab::findOrEmpty($params['id']);
            if ($crontab->isEmpty()) {
                throw new \Exception('定时任务不存在');
            }
            switch ($params['operate']) {
                case 'start';
                    $crontab->status = CrontabEnum::START;
                    break;
                case 'stop':
                    $crontab->status = CrontabEnum::STOP;
                    break;
            }
            $crontab->save();

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * 获取规则执行时间
     * @param array $params
     * @return array|string
     * @author LZH
     * @date 2025/2/19
     */
    public static function expression(array $params): array|string
    {
        try {
            $cron = new CronExpression($params['expression']);
            $result = $cron->getMultipleRunDates(5);
            $result = json_decode(json_encode($result), true);
            $lists = [];
            foreach ($result as $k => $v) {
                $lists[$k]['time'] = $k + 1;
                $lists[$k]['date'] = str_replace('.000000', '', $v['date']);
            }
            $lists[] = ['time' => 'x', 'date' => '……'];
            return $lists;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}