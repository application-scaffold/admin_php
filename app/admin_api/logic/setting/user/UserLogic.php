<?php
declare(strict_types=1);

namespace app\admin_api\logic\setting\user;

use app\common\service\{ConfigService, FileService};

/**
 * 设置-用户设置逻辑层
 * @class UserLogic
 * @package app\admin_api\logic\setting\user
 * @author LZH
 * @date 2025/2/19
 */
class UserLogic
{

    /**
     * 获取用户设置
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getConfig(): array
    {
        $defaultAvatar = config('project.default_image.user_avatar');
        $config = [
            //默认头像
            'default_avatar' => FileService::getFileUrl(ConfigService::get('default_image', 'user_avatar', $defaultAvatar)),
        ];
        return $config;
    }

    /**
     * 设置用户设置
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public function setConfig(array $params): bool
    {
        $avatar = FileService::setFileUrl($params['default_avatar']);
        ConfigService::set('default_image', 'user_avatar', $avatar);
        return true;
    }

    /**
     * 获取注册配置
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function getRegisterConfig(): array
    {
        $config = [
            // 登录方式
            'login_way' => ConfigService::get('login', 'login_way', config('project.login.login_way')),
            // 注册强制绑定手机
            'coerce_mobile' => ConfigService::get('login', 'coerce_mobile', config('project.login.coerce_mobile')),
            // 政策协议
            'login_agreement' => ConfigService::get('login', 'login_agreement', config('project.login.login_agreement')),
            // 第三方登录 开关
            'third_auth' => ConfigService::get('login', 'third_auth', config('project.login.third_auth')),
            // 微信授权登录
            'wechat_auth' => ConfigService::get('login', 'wechat_auth', config('project.login.wechat_auth')),
            // qq授权登录
            'qq_auth' => ConfigService::get('login', 'qq_auth', config('project.login.qq_auth')),
        ];
        return $config;
    }

    /**
     * 设置登录注册
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function setRegisterConfig(array $params): bool
    {
        // 登录方式：1-账号密码登录；2-手机短信验证码登录
        ConfigService::set('login', 'login_way', $params['login_way']);
        // 注册强制绑定手机
        ConfigService::set('login', 'coerce_mobile', $params['coerce_mobile']);
        // 政策协议
        ConfigService::set('login', 'login_agreement', $params['login_agreement']);
        // 第三方授权登录
        ConfigService::set('login', 'third_auth', $params['third_auth']);
        // 微信授权登录
        ConfigService::set('login', 'wechat_auth', $params['wechat_auth']);
        // qq登录
        ConfigService::set('login', 'qq_auth', $params['qq_auth']);
        return true;
    }

}