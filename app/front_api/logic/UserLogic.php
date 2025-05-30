<?php
declare(strict_types=1);

namespace app\front_api\logic;

use app\common\{enum\notice\NoticeEnum,
    enum\user\UserTerminalEnum,
    enum\YesNoEnum,
    logic\BaseLogic,
    model\user\User,
    model\user\UserAuth,
    service\FileService,
    service\sms\SmsDriver,
    service\wechat\WeChatMnpService};
use think\facade\Config;
use think\model\contract\Modelable;

/**
 * 会员逻辑层
 * @class UserLogic
 * @package app\front_api\logic
 * @author LZH
 * @date 2025/2/20
 */
class UserLogic extends BaseLogic
{

    /**
     * 个人中心
     * @param array $userInfo
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public static function center(array $userInfo): array
    {
        $user = User::where(['id' => $userInfo['user_id']])
            ->field('id,sn,sex,account,nickname,real_name,avatar,mobile,create_time,is_new_user,user_money,password')
            ->findOrEmpty();

        if (in_array($userInfo['terminal'], [UserTerminalEnum::WECHAT_MMP, UserTerminalEnum::WECHAT_OA])) {
            $auth = UserAuth::where(['user_id' => $userInfo['user_id'], 'terminal' => $userInfo['terminal']])->find();
            $user['is_auth'] = $auth ? YesNoEnum::YES : YesNoEnum::NO;
        }

        $user['has_password'] = !empty($user['password']);
        $user->hidden(['password']);
        return $user->toArray();
    }

    /**
     * 个人信息
     * @param int $userId
     * @return array
     * @author LZH
     * @date 2025/2/20
     */
    public static function info(int $userId): array
    {
        $user = User::where(['id' => $userId])
            ->field('id,sn,sex,account,password,nickname,real_name,avatar,mobile,create_time,user_money')
            ->findOrEmpty();
        $user['has_password'] = !empty($user['password']);
        $user['has_auth'] = self::hasWechatAuth($userId);
        $user['version'] = config('project.version');
        $user->hidden(['password']);
        return $user->toArray();
    }

    /**
     * 设置用户信息
     * @param int $userId
     * @param array $params
     * @return User|false|Modelable
     * @author LZH
     * @date 2025/2/20
     */
    public static function setInfo(int $userId, array $params): User|Modelable|bool
    {
        try {
            if ($params['field'] == "avatar") {
                $params['value'] = FileService::setFileUrl($params['value']);
            }

            return User::update([
                    'id' => $userId,
                    $params['field'] => $params['value']]
            );
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 是否有微信授权信息
     * @param int $userId
     * @return bool
     * @author LZH
     * @date 2025/2/20
     */
    public static function hasWechatAuth(int $userId): bool
    {
        //是否有微信授权登录
        $terminal = [UserTerminalEnum::WECHAT_MMP, UserTerminalEnum::WECHAT_OA,UserTerminalEnum::PC];
        $auth = UserAuth::where(['user_id' => $userId])
            ->whereIn('terminal', $terminal)
            ->findOrEmpty();
        return !$auth->isEmpty();
    }

    /**
     * 重置登录密码
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/20
     */
    public static function resetPassword(array $params): bool
    {
        try {
            // 校验验证码
            $smsDriver = new SmsDriver();
            if (!$smsDriver->verify($params['mobile'], $params['code'], NoticeEnum::FIND_LOGIN_PASSWORD_CAPTCHA)) {
                throw new \Exception('验证码错误');
            }

            // 重置密码
            $passwordSalt = Config::get('project.unique_identification');
            $password = create_password($params['password'], $passwordSalt);

            // 更新
            User::where('mobile', $params['mobile'])->update([
                'password' => $password
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 修稿密码
     * @param array $params
     * @param int $userId
     * @return bool
     * @author LZH
     * @date 2025/2/20
     */
    public static function changePassword(array $params, int $userId): bool
    {
        try {
            $user = User::findOrEmpty($userId);
            if ($user->isEmpty()) {
                throw new \Exception('用户不存在');
            }

            // 密码盐
            $passwordSalt = Config::get('project.unique_identification');

            if (!empty($user['password'])) {
                if (empty($params['old_password'])) {
                    throw new \Exception('请填写旧密码');
                }
                $oldPassword = create_password($params['old_password'], $passwordSalt);
                if ($oldPassword != $user['password']) {
                    throw new \Exception('原密码不正确');
                }
            }

            // 保存密码
            $password = create_password($params['password'], $passwordSalt);
            $user->password = $password;
            $user->save();

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 获取小程序手机号
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/20
     */
    public static function getMobileByMnp(array $params): bool
    {
        try {
            $response = (new WeChatMnpService())->getUserPhoneNumber($params['code']);
            $phoneNumber = $response['phone_info']['purePhoneNumber'] ?? '';
            if (empty($phoneNumber)) {
                throw new \Exception('获取手机号码失败');
            }

            $user = User::where([
                ['mobile', '=', $phoneNumber],
                ['id', '<>', $params['user_id']]
            ])->findOrEmpty();

            if (!$user->isEmpty()) {
                throw new \Exception('手机号已被其他账号绑定');
            }

            // 绑定手机号
            User::update([
                'id' => $params['user_id'],
                'mobile' => $phoneNumber
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 绑定手机号
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/20
     */
    public static function bindMobile(array $params): bool
    {
        try {
            // 变更手机号场景
            $sceneId = NoticeEnum::CHANGE_MOBILE_CAPTCHA;
            $where = [
                ['id', '=', $params['user_id']],
                ['mobile', '=', $params['mobile']]
            ];

            // 绑定手机号场景
            if ($params['type'] == 'bind') {
                $sceneId = NoticeEnum::BIND_MOBILE_CAPTCHA;
                $where = [
                    ['mobile', '=', $params['mobile']]
                ];
            }

            // 校验短信
            $checkSmsCode = (new SmsDriver())->verify($params['mobile'], $params['code'], $sceneId);
            if (!$checkSmsCode) {
                throw new \Exception('验证码错误');
            }

            $user = User::where($where)->findOrEmpty();
            if (!$user->isEmpty()) {
                throw new \Exception('该手机号已被使用');
            }

            User::update([
                'id' => $params['user_id'],
                'mobile' => $params['mobile'],
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

}