<?php
declare(strict_types=1);

namespace app\common\enum\notice;

/**
 * 通知枚举
 * @class NoticeEnum
 * @package app\common\enum\notice
 * @author LZH
 * @date 2025/2/18
 */
class NoticeEnum
{
    /**
     * 通知类型
     */
    const SYSTEM = 1;
    const SMS = 2;
    const OA = 3;
    const MNP = 4;


    /**
     * 短信验证码场景
     */
    const LOGIN_CAPTCHA = 101;
    const BIND_MOBILE_CAPTCHA = 102;
    const CHANGE_MOBILE_CAPTCHA = 103;
    const FIND_LOGIN_PASSWORD_CAPTCHA = 104;


    /**
     * 验证码场景
     */
    const SMS_SCENE = [
        self::LOGIN_CAPTCHA,
        self::BIND_MOBILE_CAPTCHA,
        self::CHANGE_MOBILE_CAPTCHA,
        self::FIND_LOGIN_PASSWORD_CAPTCHA,
    ];


    //通知类型
    const BUSINESS_NOTIFICATION = 1;//业务通知
    const VERIFICATION_CODE = 2;//验证码


    /**
     * 通知类型
     * @param bool $value
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getTypeDesc(bool $value = true): array|string
    {
        $data = [
            self::BUSINESS_NOTIFICATION => '业务通知',
            self::VERIFICATION_CODE => '验证码'
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value];
    }


    /**
     * 获取场景描述
     * @param $sceneId
     * @param bool $flag
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getSceneDesc($sceneId, bool $flag = false): array|string
    {
        $desc = [
            self::LOGIN_CAPTCHA => '登录验证码',
            self::BIND_MOBILE_CAPTCHA => '绑定手机验证码',
            self::CHANGE_MOBILE_CAPTCHA => '变更手机验证码',
            self::FIND_LOGIN_PASSWORD_CAPTCHA => '找回登录密码验证码',
        ];

        if ($flag) {
            return $desc;
        }

        return $desc[$sceneId] ?? '';
    }


    /**
     * 更具标记获取场景
     * @param string $tag
     * @return int|string
     * @author LZH
     * @date 2025/2/18
     */
    public static function getSceneByTag(string $tag): int|string
    {
        $scene = [
            // 手机验证码登录
            'YZMDL' => self::LOGIN_CAPTCHA,
            // 绑定手机号验证码
            'BDSJHM' => self::BIND_MOBILE_CAPTCHA,
            // 变更手机号验证码
            'BGSJHM' => self::CHANGE_MOBILE_CAPTCHA,
            // 找回登录密码
            'ZHDLMM' => self::FIND_LOGIN_PASSWORD_CAPTCHA,
        ];
        return $scene[$tag] ?? '';
    }


    /**
     * 获取场景变量
     * @param string $sceneId
     * @param bool $flag
     * @return array|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getVars(string $sceneId, bool $flag = false): array
    {
        $desc = [
            self::LOGIN_CAPTCHA => '验证码:code',
            self::BIND_MOBILE_CAPTCHA => '验证码:code',
            self::CHANGE_MOBILE_CAPTCHA => '验证码:code',
            self::FIND_LOGIN_PASSWORD_CAPTCHA => '验证码:code',
        ];

        if ($flag) {
            return $desc;
        }

        return isset($desc[$sceneId]) ? ['可选变量 ' . $desc[$sceneId]] : [];
    }

    /**
     * 获取系统通知示例
     * @param string $sceneId
     * @param bool $flag
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public static function getSystemExample(string $sceneId, bool $flag = false): array
    {
        $desc = [];

        if ($flag) {
            return $desc;
        }

        // TODO Array is always empty at this point
        return isset($desc[$sceneId]) ? [$desc[$sceneId]] : [];
    }


    /**
     * 获取短信通知示例
     * @param string $sceneId
     * @param bool $flag
     * @return array|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getSmsExample(string $sceneId, bool $flag = false): array
    {
        $desc = [
            self::LOGIN_CAPTCHA => '您正在登录，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。',
            self::BIND_MOBILE_CAPTCHA => '您正在绑定手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。',
            self::CHANGE_MOBILE_CAPTCHA => '您正在变更手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。',
            self::FIND_LOGIN_PASSWORD_CAPTCHA => '您正在找回登录密码，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。',
        ];

        if ($flag) {
            return $desc;
        }

        return isset($desc[$sceneId]) ? ['示例：' . $desc[$sceneId]] : [];
    }

    /**
     * 获取公众号模板消息示例
     * @param string $sceneId
     * @param bool $flag
     * @return array|mixed
     * @author LZH
     * @date 2025/2/18
     */
    public static function getOaExample(string $sceneId, bool $flag = false): mixed
    {
        $desc = [];

        if ($flag) {
            return $desc;
        }

        // TODO Array is always empty at this point
        return $desc[$sceneId] ?? [];
    }


    /**
     * 获取小程序订阅消息示例
     * @param string $sceneId
     * @param bool $flag
     * @return array|mixed
     * @author LZH
     * @date 2025/2/18
     */
    public static function getMnpExample(string $sceneId, bool $flag = false): mixed
    {
        $desc = [];

        if ($flag) {
            return $desc;
        }

        // TODO Array is always empty at this point
        return $desc[$sceneId] ?? [];
    }


    /**
     * 提示
     * @param NoticeEnum $type
     * @param string $sceneId
     * @return array|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getOperationTips(NoticeEnum $type, string $sceneId): array
    {
        // 场景变量
        $vars = self::getVars($sceneId);
        // 其他提示
        $other = [];
        $example = [];
        // 示例
        switch ($type) {
            case self::SYSTEM:
                $example = self::getSystemExample($sceneId);
                break;
            case self::SMS:
                $other[] = '生效条件：1、管理后台完成短信设置。 2、第三方短信平台申请模板。';
                $example = self::getSmsExample($sceneId);
                break;
            case self::OA:
                $other[] = '配置路径：公众号后台 > 广告与服务 > 模板消息';
                $other[] = '推荐行业：主营行业：IT科技/互联网|电子商务 副营行业：消费品/消费品';
                $example = self::getOaExample($sceneId);
                break;
            case self::MNP:
                $other[] = '配置路径：小程序后台 > 功能 > 订阅消息';
                $example = self::getMnpExample($sceneId);
                break;
        }
        return array_merge($vars, $example, $other);
    }
}