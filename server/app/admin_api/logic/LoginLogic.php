<?php

namespace app\admin_api\logic;

use app\common\logic\BaseLogic;
use app\common\model\auth\Admin;
use app\admin_api\service\AdminTokenService;
use app\common\service\FileService;
use think\facade\Config;

/**
 * 登录逻辑
 * @class LoginLogic
 * @package app\admin_api\logic
 * @author LZH
 * @date 2025/2/19
 */
class LoginLogic extends BaseLogic
{

    /**
     * 管理员账号登录
     * @param $params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function login($params)
    {
        $time = time();
        $admin = Admin::where('account', '=', $params['account'])->find();

        //用户表登录信息更新
        $admin->login_time = $time;
        $admin->login_ip = request()->ip();
        $admin->save();

        //设置token
        $adminInfo = AdminTokenService::setToken($admin->id, $params['terminal'], $admin->multipoint_login);

        //返回登录信息
        $avatar = $admin->avatar ? $admin->avatar : Config::get('project.default_image.admin_avatar');
        $avatar = FileService::getFileUrl($avatar);
        return [
            'name' => $adminInfo['name'],
            'avatar' => $avatar,
            'role_name' => $adminInfo['role_name'],
            'token' => $adminInfo['token'],
        ];
    }

    /**
     * 退出登录
     * @param $adminInfo
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function logout($adminInfo)
    {
        //token不存在，不注销
        if (!isset($adminInfo['token'])) {
            return false;
        }
        //设置token过期
        return AdminTokenService::expireToken($adminInfo['token']);
    }
}