<?php

declare (strict_types=1);

namespace app\front_api\http\middleware;

use app\common\cache\UserTokenCache;
use app\common\service\JsonService;
use app\front_api\service\UserTokenService;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Config;
use think\Request;
use think\response\Json;

class LoginMiddleware
{

    /**
     * 登录验证
     * @param Request $request
     * @param \Closure $next
     * @return mixed|Json
     * @throws \DateMalformedStringException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function handle(Request $request, \Closure $next)
    {
        $token = $request->header('token');
        //判断接口是否免登录
        $isNotNeedLogin = $request->controllerObject->isNotNeedLogin();

        //不直接判断$isNotNeedLogin结果，使不需要登录的接口通过，为了兼容某些接口可以登录或不登录访问
        if (empty($token) && !$isNotNeedLogin) {
            //没有token并且该地址需要登录才能访问, 指定show为0，前端不弹出此报错
            return JsonService::fail('请求参数缺token', [], 0, 0);
        }

        $userInfo = (new UserTokenCache())->getUserInfo($token);

        if (empty($userInfo) && !$isNotNeedLogin) {
            //token过期无效并且该地址需要登录才能访问
            return JsonService::fail('登录超时，请重新登录', [], -1, 0);
        }

        //token临近过期，自动续期
        if ($userInfo) {
            //获取临近过期自动续期时长
            $beExpireDuration = Config::get('project.user_token.be_expire_duration');
            //token续期
            if (time() > ($userInfo['expire_time'] - $beExpireDuration)) {
                $result = UserTokenService::overtimeToken($token);
                //续期失败（数据表被删除导致）
                if (empty($result)) {
                    return JsonService::fail('登录过期', [], -1);
                }
            }
        }

        //给request赋值，用于控制器
        $request->userInfo = $userInfo;
        $request->userId = $userInfo['user_id'] ?? 0;

        return $next($request);
    }

}