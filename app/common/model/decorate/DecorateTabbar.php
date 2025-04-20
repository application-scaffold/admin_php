<?php
declare (strict_types = 1);

namespace app\common\model\decorate;

use app\common\model\BaseModel;
use app\common\service\FileService;

/**
 * 装修配置-底部导航
 * @class DecorateTabbar
 * @package app\common\model\decorate
 * @author LZH
 * @date 2025/2/18
 */
class DecorateTabbar extends BaseModel
{
    // 设置json类型字段
    protected array $json = ['link'];

    // 设置JSON数据返回数组
    protected bool $jsonAssoc = true;


    /**
     * 获取底部导航列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/18
     */
    public static function getTabbarLists(): array
    {
        $tabbar = self::select()->toArray();

        if (empty($tabbar)) {
           return $tabbar;
        }

        foreach ($tabbar as &$item) {
            if (!empty($item['selected'])) {
                $item['selected'] = FileService::getFileUrl($item['selected']);
            }
            if (!empty($item['unselected'])) {
                $item['unselected'] = FileService::getFileUrl($item['unselected']);
            }
        }

        return $tabbar;
    }
}