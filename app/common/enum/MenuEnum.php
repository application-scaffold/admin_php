<?php
declare(strict_types=1);

namespace app\common\enum;

/**
 * @class MenuEnum
 * @package app\common\enum
 * @author LZH
 * @date 2025/2/18
 */
class MenuEnum
{
    //商城页面
    const SHOP_PAGE = [
        [
            'index'     => 1,
            'name'      => '首页',
            'path'      => '/pages/index/index',
            'params'    => [],
            'type'      => 'shop',
        ],
    ];


    //菜单类型
    const NAVIGATION_HOME = 1;//首页导航
    const NAVIGATION_PERSONAL = 2;//个人中心

    //链接类型
    const LINK_SHOP = 1;//商城页面
    const LINK_CATEGORY = 2;//分类页面
    const LINK_CUSTOM = 3;//自定义链接

    /**
     * 链接类型
     * @param bool|string $value
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getLinkDesc(mixed $value = true): array|string
    {
        $data = [
            self::LINK_SHOP => '商城页面',
            self::LINK_CATEGORY => '分类页面',
            self::LINK_CUSTOM => '自定义链接'
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value];
    }
}