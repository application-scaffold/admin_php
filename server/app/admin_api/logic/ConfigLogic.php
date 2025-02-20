<?php

namespace app\admin_api\logic;

use app\admin_api\logic\article\ArticleCateLogic;
use app\admin_api\logic\auth\MenuLogic;
use app\admin_api\logic\auth\RoleLogic;
use app\admin_api\logic\dept\DeptLogic;
use app\admin_api\logic\dept\JobsLogic;
use app\admin_api\logic\setting\dict\DictTypeLogic;
use app\common\enum\YesNoEnum;
use app\common\model\article\ArticleCate;
use app\common\model\auth\SystemMenu;
use app\common\model\auth\SystemRole;
use app\common\model\dept\Dept;
use app\common\model\dept\Jobs;
use app\common\model\dict\DictData;
use app\common\model\dict\DictType;
use app\common\service\{FileService, ConfigService};

/**
 * 配置类逻辑层
 * @class ConfigLogic
 * @package app\admin_api\logic
 * @author LZH
 * @date 2025/2/19
 */
class ConfigLogic
{

    /**
     * 获取配置
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getConfig(): array
    {
        $config = [
            // 文件域名
            'oss_domain' => FileService::getFileUrl(),

            // 网站名称
            'web_name' => ConfigService::get('website', 'name'),
            // 网站图标
            'web_favicon' => FileService::getFileUrl(ConfigService::get('website', 'web_favicon')),
            // 网站logo
            'web_logo' => FileService::getFileUrl(ConfigService::get('website', 'web_logo')),
            // 登录页
            'login_image' => FileService::getFileUrl(ConfigService::get('website', 'login_image')),
            // 版权信息
            'copyright_config' => ConfigService::get('copyright', 'config', []),
            // 版本号
            'version' => config('project.version')
        ];
        return $config;
    }


    /**
     * 根据类型获取字典类型
     * @param $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function getDictByType($type)
    {
        if (!is_string($type)) {
            return [];
        }
        
        $type = explode(',', $type);
        $lists = DictData::whereIn('type_value', $type)->select()->toArray();

        if (empty($lists)) {
            return [];
        }

        $result = [];
        foreach ($type as $item) {
            foreach ($lists as $dict) {
                if ($dict['type_value'] == $item) {
                    $result[$item][] = $dict;
                }
            }
        }
        return $result;
    }

}