<?php


namespace app\admin_api\service;

use app\common\cache\AdminTokenCache;
use app\common\model\auth\AdminSession;
use think\facade\Config;


/**
 * 管理员token
 * @class AdminTokenService
 * @package app\admin_api\service
 * @author LZH
 * @date 2025/2/19
 */
class AdminTokenService
{

    /**
     * 设置或更新管理员token
     * @param $adminId
     * @param $terminal
     * @param $multipointLogin
     * @return array|false|mixed
     * @throws \DateMalformedStringException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function setToken($adminId, $terminal, $multipointLogin = 1)
    {
        $time = time();
        $adminSession = AdminSession::where([['admin_id', '=', $adminId], ['terminal', '=', $terminal]])->find();

        //获取token延长过期的时间
        $expireTime = $time + Config::get('project.admin_token.expire_duration');
        $adminTokenCache = new AdminTokenCache();

        //token处理
        if ($adminSession) {
            if ($adminSession->expire_time < $time || $multipointLogin === 0) {
                //清空缓存
                $adminTokenCache->deleteAdminInfo($adminSession->token);
                //如果token过期或账号设置不支持多处登录，更新token
                $adminSession->token = create_token($adminId);
            }
            $adminSession->expire_time = $expireTime;
            $adminSession->update_time = $time;

            $adminSession->save();
        } else {
            //找不到在该终端的token记录，创建token记录
            $adminSession = AdminSession::create([
                'admin_id' => $adminId,
                'terminal' => $terminal,
                'token' => create_token($adminId),
                'expire_time' => $expireTime
            ]);
        }

        return $adminTokenCache->setAdminInfo($adminSession->token);
    }


    /**
     * 延长token过期时间
     * @param $token
     * @return array|false|mixed
     * @throws \DateMalformedStringException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function overtimeToken($token)
    {
        $time = time();
        $adminSession = AdminSession::where('token', '=', $token)->findOrEmpty();
        if ($adminSession->isEmpty()) {
            return false;
        }
        //延长token过期时间
        $adminSession->expire_time = $time + Config::get('project.admin_token.expire_duration');
        $adminSession->update_time = $time;
        $adminSession->save();
        return (new AdminTokenCache())->setAdminInfo($adminSession->token);
    }

    /**
     * 设置token为过期
     * @param $token
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function expireToken($token)
    {
        $adminSession = AdminSession::where('token', '=', $token)
            ->with('admin')
            ->findOrEmpty();

        if ($adminSession->isEmpty()) {
            return false;
        }

        //当支持多处登录的时候，服务端不注销
        if ($adminSession->admin->multipoint_login === 1) {
            return false;
        }

        $time = time();
        $adminSession->expire_time = $time;
        $adminSession->update_time = $time;
        $adminSession->save();

        return (new AdminTokenCache())->deleteAdminInfo($token);
    }

}