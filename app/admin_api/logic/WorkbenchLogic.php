<?php
declare(strict_types=1);

namespace app\admin_api\logic;


use app\common\logic\BaseLogic;
use app\common\service\ConfigService;
use app\common\service\FileService;


/**
 * 工作台
 * @class WorkbenchLogic
 * @package app\admin_api\logic
 * @author LZH
 * @date 2025/2/19
 */
class WorkbenchLogic extends BaseLogic
{

    /**
     * 工作套
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function index(): array
    {
        return [
            // 版本信息
            'version' => self::versionInfo(),
            // 今日数据
            'today' => self::today(),
            // 常用功能
            'menu' => self::menu(),
            // 近15日访客数
            'visitor' => self::visitor(),
            // 服务支持
            'support' => self::support(),
            // 销售数据
            'sale' => self::sale()
        ];
    }


    /**
     * 常用功能
     * @return array[]
     * @author LZH
     * @date 2025/2/19
     */
    public static function menu(): array
    {
        return [
            [
                'name' => '管理员',
                'image' => FileService::getFileUrl(config('project.default_image.menu_admin')),
                'url' => '/permission/admin'
            ],
            [
                'name' => '角色管理',
                'image' => FileService::getFileUrl(config('project.default_image.menu_role')),
                'url' => '/permission/role'
            ],
            [
                'name' => '部门管理',
                'image' => FileService::getFileUrl(config('project.default_image.menu_dept')),
                'url' => '/organization/department'
            ],
            [
                'name' => '字典管理',
                'image' => FileService::getFileUrl(config('project.default_image.menu_dict')),
                'url' => '/dev_tools/dict'
            ],
            [
                'name' => '代码生成器',
                'image' => FileService::getFileUrl(config('project.default_image.menu_generator')),
                'url' => '/dev_tools/code'
            ],
            [
                'name' => '素材中心',
                'image' => FileService::getFileUrl(config('project.default_image.menu_file')),
                'url' => '/material/index'
            ],
            [
                'name' => '菜单权限',
                'image' => FileService::getFileUrl(config('project.default_image.menu_auth')),
                'url' => '/permission/menu'
            ],
            [
                'name' => '网站信息',
                'image' => FileService::getFileUrl(config('project.default_image.menu_web')),
                'url' => '/setting/website/information'
            ],
        ];
    }


    /**
     * 版本信息
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function versionInfo(): array
    {
        return [
            'version' => config('project.version'),
            'website' => config('project.website.url'),
            'name' => ConfigService::get('website', 'name'),
            'based' => 'vue3.x、ElementUI、MySQL',
            'channel' => [
                'website' => 'https://github.com/lzh06550107',
                'gitee' => 'https://github.com/application-scaffold/admin_php',
            ]
        ];
    }

    /**
     * 今日数据
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function today(): array
    {
        return [
            'time' => date('Y-m-d H:i:s'),
            // 今日销售额
            'today_sales' => 100,
            // 总销售额
            'total_sales' => 1000,

            // 今日访问量
            'today_visitor' => 10,
            // 总访问量
            'total_visitor' => 100,

            // 今日新增用户量
            'today_new_user' => 30,
            // 总用户量
            'total_new_user' => 3000,

            // 订单量 (笔)
            'order_num' => 12,
            // 总订单量
            'order_sum' => 255
        ];
    }


    /**
     * 访问数
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function visitor(): array
    {
        $num = [];
        $date = [];
        for ($i = 0; $i < 15; $i++) {
            $where_start = strtotime("- " . $i . "day");
            $date[] = date('m/d', $where_start);
            $num[$i] = rand(0, 100);
        }

        return [
            'date' => $date,
            'list' => [
                ['name' => '访客数', 'data' => $num]
            ]
        ];
    }

    /**
     * 访问数
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function sale(): array
    {
        $num = [];
        $date = [];
        for ($i = 0; $i < 7; $i++) {
            $where_start = strtotime("- " . $i . "day");
            $date[] = date('m/d', $where_start);
            $num[$i] = rand(30, 200);
        }

        return [
            'date' => $date,
            'list' => [
                ['name' => '销售量', 'data' => $num]
            ]
        ];
    }


    /**
     * 服务支持
     * @return array[]
     * @author LZH
     * @date 2025/2/19
     */
    public static function support(): array
    {
        return [
            [
                'image' => FileService::getFileUrl(config('project.default_image.qq_group')),
                'title' => '官方公众号',
                'desc' => '关注官方公众号',
            ],
            [
                'image' => FileService::getFileUrl(config('project.default_image.customer_service')),
                'title' => '添加企业客服微信',
                'desc' => '想了解更多请添加客服',
            ]
        ];
    }

}