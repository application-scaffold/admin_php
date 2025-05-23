<?php
declare(strict_types=1);

namespace app\front_api\validate;

use app\common\cache\UserAccountSafeCache;
use app\common\enum\LoginEnum;
use app\common\enum\notice\NoticeEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\enum\YesNoEnum;
use app\common\service\ConfigService;
use app\common\service\sms\SmsDriver;
use app\common\validate\BaseValidate;
use app\common\model\user\User;
use think\facade\Config;

/**
 * 账号密码登录校验
 * @class LoginAccountValidate
 * @package app\front_api\validate
 * @author LZH
 * @date 2025/2/20
 */
class LoginAccountValidate extends BaseValidate
{

    protected $rule = [
        'terminal' => 'require|in:' . UserTerminalEnum::WECHAT_MMP . ',' . UserTerminalEnum::WECHAT_OA . ','
            . UserTerminalEnum::H5 . ',' . UserTerminalEnum::PC . ',' . UserTerminalEnum::IOS .
            ',' . UserTerminalEnum::ANDROID,
        'scene' => 'require|in:' . LoginEnum::ACCOUNT_PASSWORD . ',' . LoginEnum::MOBILE_CAPTCHA . '|checkConfig',
        'account' => 'require',
    ];


    protected $message = [
        'terminal.require' => '终端参数缺失',
        'terminal.in' => '终端参数状态值不正确',
        'scene.require' => '场景不能为空',
        'scene.in' => '场景值错误',
        'account.require' => '请输入账号',
        'password.require' => '请输入密码',
    ];


    /**
     * 登录场景相关校验
     * @param string $scene
     * @param string $rule
     * @param array $data
     * @return bool|string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkConfig(string $scene, string $rule, array $data): bool|string
    {
        $config = ConfigService::get('login', 'login_way');
        if (!in_array($scene, $config)) {
            return '不支持的登录方式';
        }

        // 账号密码登录
        if (LoginEnum::ACCOUNT_PASSWORD == $scene) {
            if (!isset($data['password'])) {
                return '请输入密码';
            }
            return $this->checkPassword($data['password'], [], $data);
        }

        // 手机验证码登录
        if (LoginEnum::MOBILE_CAPTCHA == $scene) {
            if (!isset($data['code'])) {
                return '请输入手机验证码';
            }
            return $this->checkCode($data['code'], [], $data);
        }

        return true;
    }

    /**
     * 登录密码校验
     * @param string $password
     * @param string $other
     * @param array $data
     * @return string|true
     * @author LZH
     * @date 2025/2/20
     */
    public function checkPassword(string $password, string $other, array $data): bool|string
    {
        //账号安全机制，连续输错后锁定，防止账号密码暴力破解
        $userAccountSafeCache = new UserAccountSafeCache();
        if (!$userAccountSafeCache->isSafe()) {
            return '密码连续' . $userAccountSafeCache->count . '次输入错误，请' . $userAccountSafeCache->minute . '分钟后重试';
        }

        $where = [];
        if ($data['scene'] == LoginEnum::ACCOUNT_PASSWORD) {
            // 手机号密码登录
            $where = ['account|mobile' => $data['account']];
        }

        $userInfo = User::where($where)
            ->field(['password,is_disable'])
            ->findOrEmpty();

        if ($userInfo->isEmpty()) {
            return '用户不存在';
        }

        if ($userInfo['is_disable'] === YesNoEnum::YES) {
            return '用户已禁用';
        }

        if (empty($userInfo['password'])) {
            $userAccountSafeCache->record();
            return '用户不存在';
        }

        $passwordSalt = Config::get('project.unique_identification');
        if ($userInfo['password'] !== create_password($password, $passwordSalt)) {
            $userAccountSafeCache->record();
            return '密码错误';
        }

        $userAccountSafeCache->relieve();

        return true;
    }

    /**
     * 校验验证码
     * @param string $code
     * @param string $rule
     * @param array $data
     * @return string|true
     * @author LZH
     * @date 2025/2/20
     */
    public function checkCode(string $code, string $rule, array $data): bool|string
    {
        $smsDriver = new SmsDriver();
        $result = $smsDriver->verify($data['account'], $code, NoticeEnum::LOGIN_CAPTCHA);
        if ($result) {
            return true;
        }
        return '验证码错误';
    }
}