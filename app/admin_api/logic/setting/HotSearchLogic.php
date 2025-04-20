<?php
declare(strict_types=1);

namespace app\admin_api\logic\setting;

use app\common\logic\BaseLogic;
use app\common\model\HotSearch;
use app\common\service\ConfigService;
use app\common\service\FileService;


/**
 * 热门搜素逻辑
 * @class HotSearchLogic
 * @package app\admin_api\logic\setting
 * @author LZH
 * @date 2025/2/19
 */
class HotSearchLogic extends BaseLogic
{

    /**
     * 获取配置
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function getConfig(): array
    {
        return [
            // 功能状态 0-关闭 1-开启
            'status' => ConfigService::get('hot_search', 'status', 0),
            // 热门搜索数据
            'data' => HotSearch::field(['name', 'sort'])->order(['sort' => 'desc', 'id' =>'desc'])->select()->toArray(),
        ];
    }


    /**
     * 设置热门搜搜
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function setConfig(array $params): bool
    {
        try {
            if (!empty($params['data'])) {
                $model = (new HotSearch());
                $model->where('id', '>', 0)->delete();
                $model->saveAll($params['data']);
            }

            $status = empty($params['status']) ? 0 : $params['status'];
            ConfigService::set('hot_search', 'status', $status);

            return true;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

}