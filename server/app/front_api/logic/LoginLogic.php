<?php

namespace app\front_api\logic;

use app\common\cache\WebScanLoginCache;
use app\common\logic\BaseLogic;
use app\front_api\service\{UserTokenService, WechatUserService};
use app\common\enum\{LoginEnum, user\UserTerminalEnum, YesNoEnum};
use app\common\service\{
    ConfigService,
    FileService,
    wechat\WeChatConfigService,
    wechat\WeChatMnpService,
    wechat\WeChatOaService,
    wechat\WeChatRequestService
};
use app\common\model\user\{User, UserAuth};
use think\facade\{Db, Config};

/**
 * 登录逻辑
 * @class LoginLogic
 * @package app\front_api\logic
 * @author LZH
 * @date 2025/2/19
 */
class LoginLogic extends BaseLogic
{

    /**
     * 账号密码注册
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function register(array $params)
    {
        try {
            $userSn = User::createUserSn();
            $passwordSalt = Config::get('project.unique_identification');
            $password = create_password($params['password'], $passwordSalt);
            $avatar = ConfigService::get('default_image', 'user_avatar');

            User::create([
                'sn' => $userSn,
                'avatar' => $avatar,
                'nickname' => '用户' . $userSn,
                'account' => $params['account'],
                'password' => $password,
                'channel' => $params['channel'],
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 账号/手机号登录，手机号验证码
     * @param $params
     * @return array|false
     * @author LZH
     * @date 2025/2/19
     */
    public static function login($params)
    {
        try {
            // 账号/手机号 密码登录
            $where = ['account|mobile' => $params['account']];
            if ($params['scene'] == LoginEnum::MOBILE_CAPTCHA) {
                //手机验证码登录
                $where = ['mobile' => $params['account']];
            }

            $user = User::where($where)->findOrEmpty();
            if ($user->isEmpty()) {
                throw new \Exception('用户不存在');
            }

            //更新登录信息
            $user->login_time = time();
            $user->login_ip = request()->ip();
            $user->save();

            //设置token
            $userInfo = UserTokenService::setToken($user->id, $params['terminal']);

            //返回登录信息
            $avatar = $user->avatar ?: Config::get('project.default_image.user_avatar');
            $avatar = FileService::getFileUrl($avatar);

            return [
                'nickname' => $userInfo['nickname'],
                'sn' => $userInfo['sn'],
                'mobile' => $userInfo['mobile'],
                'avatar' => $avatar,
                'token' => $userInfo['token'],
            ];
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 退出登录
     * @param $userInfo
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public static function logout($userInfo)
    {
        //token不存在，不注销
        if (!isset($userInfo['token'])) {
            return false;
        }

        //设置token过期
        return UserTokenService::expireToken($userInfo['token']);
    }


    /**
     * 获取微信请求code的链接
     * @param string $url
     * @return mixed
     * @author LZH
     * @date 2025/2/20
     */
    public static function codeUrl(string $url)
    {
        return (new WeChatOaService())->getCodeUrl($url);
    }

    /**
     * 公众号登录
     * @param array $params
     * @return array|false
     * @author LZH
     * @date 2025/2/20
     */
    public static function oaLogin(array $params)
    {
        Db::startTrans();
        try {
            //通过code获取微信 openid
            $response = (new WeChatOaService())->getOaResByCode($params['code']);
            $userServer = new WechatUserService($response, UserTerminalEnum::WECHAT_OA);
            $userInfo = $userServer->getResopnseByUserInfo()->authUserLogin()->getUserInfo();

            // 更新登录信息
            self::updateLoginInfo($userInfo['id']);

            Db::commit();
            return $userInfo;

        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 小程序-静默登录
     * @param array $params
     * @return array|false
     * @author LZH
     * @date 2025/2/20
     */
    public static function silentLogin(array $params)
    {
        try {
            //通过code获取微信 openid
            $response = (new WeChatMnpService())->getMnpResByCode($params['code']);
            $userServer = new WechatUserService($response, UserTerminalEnum::WECHAT_MMP);
            $userInfo = $userServer->getResopnseByUserInfo('silent')->getUserInfo();

            if (!empty($userInfo)) {
                // 更新登录信息
                self::updateLoginInfo($userInfo['id']);
            }

            return $userInfo;
        } catch (\Exception  $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 小程序-授权登录
     * @param array $params
     * @return array|false
     * @author LZH
     * @date 2025/2/20
     */
    public static function mnpLogin(array $params)
    {
        Db::startTrans();
        try {
            //通过code获取微信 openid
            $response = (new WeChatMnpService())->getMnpResByCode($params['code']);
            $userServer = new WechatUserService($response, UserTerminalEnum::WECHAT_MMP);
            $userInfo = $userServer->getResopnseByUserInfo()->authUserLogin()->getUserInfo();

            // 更新登录信息
            self::updateLoginInfo($userInfo['id']);

            Db::commit();
            return $userInfo;
        } catch (\Exception  $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 更新登录信息
     * @param $userId
     * @return void
     * @throws \Exception
     * @author LZH
     * @date 2025/2/20
     */
    public static function updateLoginInfo($userId)
    {
        $user = User::findOrEmpty($userId);
        if ($user->isEmpty()) {
            throw new \Exception('用户不存在');
        }

        $time = time();
        $user->login_time = $time;
        $user->login_ip = request()->ip();
        $user->update_time = $time;
        $user->save();
    }

    /**
     * 小程序端绑定微信
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/20
     */
    public static function mnpAuthLogin(array $params)
    {
        try {
            //通过code获取微信openid
            $response = (new WeChatMnpService())->getMnpResByCode($params['code']);
            $response['user_id'] = $params['user_id'];
            $response['terminal'] = UserTerminalEnum::WECHAT_MMP;

            return self::createAuth($response);

        } catch (\Exception  $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 公众号端绑定微信
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/20
     */
    public static function oaAuthLogin(array $params)
    {
        try {
            //通过code获取微信openid
            $response = (new WeChatOaService())->getOaResByCode($params['code']);
            $response['user_id'] = $params['user_id'];
            $response['terminal'] = UserTerminalEnum::WECHAT_OA;

            return self::createAuth($response);

        } catch (\Exception  $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 生成授权记录
     * @param $response
     * @return true
     * @throws \Exception
     * @author LZH
     * @date 2025/2/20
     */
    public static function createAuth($response)
    {
        //先检查openid是否有记录
        $isAuth = UserAuth::where('openid', '=', $response['openid'])->findOrEmpty();
        if (!$isAuth->isEmpty()) {
            throw new \Exception('该微信已被绑定');
        }

        if (isset($response['unionid']) && !empty($response['unionid'])) {
            //在用unionid找记录，防止生成两个账号，同个unionid的问题
            $userAuth = UserAuth::where(['unionid' => $response['unionid']])
                ->findOrEmpty();
            if (!$userAuth->isEmpty() && $userAuth->user_id != $response['user_id']) {
                throw new \Exception('该微信已被绑定');
            }
        }

        //如果没有授权，直接生成一条微信授权记录
        UserAuth::create([
            'user_id' => $response['user_id'],
            'openid' => $response['openid'],
            'unionid' => $response['unionid'] ?? '',
            'terminal' => $response['terminal'],
        ]);
        return true;
    }

    /**
     * 获取扫码登录地址
     * @param $redirectUri
     * @return array|false
     * @author LZH
     * @date 2025/2/20
     */
    public static function getScanCode($redirectUri)
    {
        try {
            $config = WeChatConfigService::getOpConfig();
            $appId = $config['app_id'];
            $redirectUri = UrlEncode($redirectUri);

            // 设置有效时间标记状态, 超时扫码不可登录
            $state = MD5(time().rand(10000, 99999));
            (new WebScanLoginCache())->setScanLoginState($state);

            // 扫码地址
            $url = WeChatRequestService::getScanCodeUrl($appId, $redirectUri, $state);
            return ['url' => $url];

        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }


    /**
     * 网站扫码登录
     * @param $params
     * @return array|false
     * @author LZH
     * @date 2025/2/20
     */
    public static function scanLogin($params)
    {
        Db::startTrans();
        try {
            // 通过code 获取 access_token,openid,unionid等信息
            $userAuth = WeChatRequestService::getUserAuthByCode($params['code']);

            if (empty($userAuth['openid']) || empty($userAuth['access_token'])) {
                throw new \Exception('获取用户授权信息失败');
            }

            // 获取微信用户信息
            $response = WeChatRequestService::getUserInfoByAuth($userAuth['access_token'], $userAuth['openid']);

            // 生成用户或更新用户信息
            $userServer = new WechatUserService($response, UserTerminalEnum::PC);
            $userInfo = $userServer->getResopnseByUserInfo()->authUserLogin()->getUserInfo();

            // 更新登录信息
            self::updateLoginInfo($userInfo['id']);

            Db::commit();
            return $userInfo;

        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 更新用户信息
     * @param $params
     * @param $userId
     * @return User|\think\model\contract\Modelable
     * @author LZH
     * @date 2025/2/20
     */
    public static function updateUser($params, $userId)
    {
        return User::where(['id' => $userId])->update([
            'nickname' => $params['nickname'],
            'avatar' => FileService::setFileUrl($params['avatar']),
            'is_new_user' => YesNoEnum::NO
        ]);
    }
}