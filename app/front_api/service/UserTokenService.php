<?php
declare(strict_types=1);

namespace app\front_api\service;

use app\common\cache\UserTokenCache;
use app\common\model\user\UserSession;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Config;

class UserTokenService
{

    /**
     * 设置或更新用户token
     * @param int $userId
     * @param string $terminal
     * @return array|false|mixed
     * @throws \DateMalformedStringException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public static function setToken(int $userId, int $terminal): mixed
    {
        $time = time();
        $userSession = UserSession::where([['user_id', '=', $userId], ['terminal', '=', $terminal]])->find();

        //获取token延长过期的时间
        $expireTime = $time + Config::get('project.user_token.expire_duration');
        $userTokenCache = new UserTokenCache();

        //token处理
        if ($userSession) {
            //清空缓存
            $userTokenCache->deleteUserInfo($userSession->token);
            //重新获取token
            $userSession->token = create_token($userId);
            $userSession->expire_time = $expireTime;
            $userSession->update_time = $time;
            $userSession->save();
        } else {
            //找不到在该终端的token记录，创建token记录
            $userSession = UserSession::create([
                'user_id' => $userId,
                'terminal' => $terminal,
                'token' => create_token($userId),
                'expire_time' => $expireTime
            ]);
        }

        return $userTokenCache->setUserInfo($userSession->token);
    }


    /**
     * 延长token过期时间
     * @param string $token
     * @return array|false|mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws \DateMalformedStringException
     * @author LZH
     * @date 2025/2/20
     */
    public static function overtimeToken(string $token): mixed
    {
        $time = time();
        $userSession = UserSession::where('token', '=', $token)->find();
        if ($userSession->isEmpty()) {
            return false;
        }
        //延长token过期时间
        $userSession->expire_time = $time + Config::get('project.user_token.expire_duration');
        $userSession->update_time = $time;
        $userSession->save();

        return (new UserTokenCache())->setUserInfo($userSession->token);
    }

    /**
     * 设置token为过期
     * @param string $token
     * @return bool
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public static function expireToken(string $token): bool
    {
        $userSession = UserSession::where('token', '=', $token)
            ->find();
        if (empty($userSession)) {
            return false;
        }

        $time = time();
        $userSession->expire_time = $time;
        $userSession->update_time = $time;
        $userSession->save();

        return (new  UserTokenCache())->deleteUserInfo($token);
    }

}