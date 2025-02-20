<?php

namespace app\admin_api\logic\setting\system;

use app\common\logic\BaseLogic;
use think\facade\Cache;

/**
 * 系统缓存逻辑
 * @class CacheLogic
 * @package app\admin_api\logic\setting\system
 * @author LZH
 * @date 2025/2/19
 */
class CacheLogic extends BaseLogic
{

    /**
     * 清楚系统缓存
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function clear()
    {
       Cache::clear();
       del_target_dir(app()->getRootPath().'runtime/file',true);
    }
}