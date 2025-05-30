<?php

declare (strict_types=1);

namespace app\admin_api\http\middleware;

use app\common\cache\AdminTokenCache;
use app\admin_api\service\AdminTokenService;
use app\common\service\JsonService;
use think\facade\Config;
use think\Request;

/**
 * 登录中间件
 * @class LoginMiddleware
 * @package app\admin_api\http\middleware
 * @author LZH
 * @date 2025/2/19
 */
class LoginMiddleware
{

    /**
     * 登录验证
     * @param $request
     * @param \Closure $next
     * @return mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function handle(Request $request, \Closure $next): mixed
    {
        $token = $request->header('token');
        //判断接口是否免登录
        $isNotNeedLogin = $request->controllerObject->isNotNeedLogin();

        //不直接判断$isNotNeedLogin结果，使不需要登录的接口通过，为了兼容某些接口可以登录或不登录访问
        if (empty($token) && !$isNotNeedLogin) {
            //没有token并且该地址需要登录才能访问
            return JsonService::fail('请求参数缺token', [], 0, 0);
        }

        // 当 $token 不空，则获取登录用户信息
        if ($token) {
            $adminInfo = (new AdminTokenCache())->getAdminInfo($token);
            if (empty($adminInfo) && !$isNotNeedLogin) {
                //token过期无效并且该地址需要登录才能访问
                return JsonService::fail('登录超时，请重新登录', [], -1);
            }

            //token临近过期，自动续期
            if ($adminInfo) {
                //获取临近过期自动续期时长
                $beExpireDuration = Config::get('project.admin_token.be_expire_duration');
                //token续期
                if (time() > ($adminInfo['expire_time'] - $beExpireDuration)) {
                    $result = AdminTokenService::overtimeToken($token);
                    //续期失败（数据表被删除导致）
                    if (empty($result)) {
                        return JsonService::fail('登录过期', [], -1);
                    }
                }
            }

            //给request赋值，用于控制器
            $request->adminInfo = $adminInfo;
            $request->adminId = $adminInfo['admin_id'] ?? 0;
        }

        return $next($request);
    }

}