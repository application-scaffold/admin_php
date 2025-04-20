<?php
declare(strict_types=1);

namespace app\admin_api\validate\setting;

use app\common\validate\BaseValidate;

/**
 * 网站设置验证器
 * @class WebSettingValidate
 * @package app\admin_api\validate\setting
 * @author LZH
 * @date 2025/2/19
 */
class WebSettingValidate extends BaseValidate
{
    protected $rule = [
        'name' => 'require|max:30',
        'web_favicon' => 'require',
        'web_logo' => 'require',
        'login_image' => 'require',
        'shop_name' => 'require',
        'shop_logo' => 'require',
        'pc_logo' => 'require'
    ];

    protected $message = [
        'name.require' => '请填写网站名称',
        'name.max' => '网站名称最长为12个字符',
        'web_favicon.require' => '请上传网站图标',
        'web_logo.require' => '请上传网站logo',
        'login_image.require' => '请上传登录页广告图',
        'shop_name.require' => '请填写前台名称',
        'shop_logo.require' => '请上传前台logo',
        'pc_logo.require' => '请上传PC端logo'
    ];

    protected $scene = [
        'website' => ['name', 'web_favicon', 'web_logo', 'login_image', 'shop_name', 'shop_logo', 'pc_logo'],
        'siteStatistics' => [''],
    ];
}