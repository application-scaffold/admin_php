<?php
declare(strict_types=1);

namespace app\common\cache;

use app\common\model\user\User;
use app\common\model\user\UserSession;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 用户token缓存
 * @class UserTokenCache
 * @package app\common\cache
 * @author LZH
 * @date 2025/2/18
 */
class UserTokenCache extends BaseCache
{

    private string $prefix = 'token_user_';

    /**
     * 通过token获取缓存用户信息
     * @param string $token
     * @return array|false|mixed
     * @throws \DateMalformedStringException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author LZH
     * @date 2025/2/18
     */
    public function getUserInfo(string $token): mixed
    {
        //直接从缓存获取
        $userInfo = $this->get($this->prefix . $token);
        if ($userInfo) {
            return $userInfo;
        }

        //从数据获取信息被设置缓存(可能后台清除缓存）
        $userInfo = $this->setUserInfo($token);
        if ($userInfo) {
            return $userInfo;
        }

        return false;
    }

    /**
     * 通过有效token设置用户信息缓存
     * @param string $token
     * @return array|false|mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws \DateMalformedStringException
     * @author LZH
     * @date 2025/2/18
     */
    public function setUserInfo(string $token): mixed
    {
        $userSession = UserSession::where([['token', '=', $token], ['expire_time', '>', time()]])->find();
        if (empty($userSession)) {
            return [];
        }

        $user = User::where('id', '=', $userSession->user_id)
            ->find();

        $userInfo = [
            'user_id' => $user->id,
            'nickname' => $user->nickname,
            'token' => $token,
            'sn' => $user->sn,
            'mobile' => $user->mobile,
            'avatar' => $user->avatar,
            'terminal' => $userSession->terminal,
            'expire_time' => $userSession->expire_time,
        ];

        $ttl = new \DateTime(Date('Y-m-d H:i:s', $userSession->expire_time));
        $this->set($this->prefix . $token, $userInfo, $ttl);
        return $this->getUserInfo($token);
    }

    /**
     * 删除缓存
     * @param string $token
     * @return bool
     * @author LZH
     * @date 2025/2/18
     */
    public function deleteUserInfo(string $token): bool
    {
        return $this->delete($this->prefix . $token);
    }

}