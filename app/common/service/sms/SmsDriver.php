<?php
declare(strict_types=1);

namespace app\common\service\sms;

use app\common\enum\notice\NoticeEnum;
use app\common\enum\notice\SmsEnum;
use app\common\enum\YesNoEnum;
use app\common\model\Notice;
use app\common\model\notice\SmsLog;
use app\common\service\ConfigService;

/**
 * 短信驱动
 * @class SmsDriver
 * @package app\common\service\sms
 * @author LZH
 * @date 2025/2/19
 */
class SmsDriver
{
    /**
     * 错误信息
     * @var
     */
    protected ?string $error = null;

    /**
     * 默认短信引擎
     * @var
     */
    protected $defaultEngine;

    /**
     * 短信引擎
     * @var
     */
    protected $engine;

    /**
     * 架构方法
     * SmsDriver constructor.
     */
    public function __construct()
    {
        // 初始化
        $this->initialize();
    }

    /**
     * 初始化
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public function initialize(): bool
    {
        try {
            $defaultEngine = ConfigService::get('sms', 'engine', false);
            if($defaultEngine === false) {
                throw new \Exception('请开启短信配置');
            }
            $this->defaultEngine = $defaultEngine;
            $classSpace = __NAMESPACE__ . '\\engine\\' . ucfirst(strtolower($defaultEngine)) . 'Sms';
            if (!class_exists($classSpace)) {
                throw new \Exception('没有相应的短信驱动类');
            }
            $engineConfig = ConfigService::get('sms', strtolower($defaultEngine), false);
            if($engineConfig === false) {
                throw new \Exception($defaultEngine . '未配置');
            }
            if ($engineConfig['status'] != 1) {
                throw new \Exception('短信服务未开启');
            }
            $this->engine = new $classSpace($engineConfig);
            if(!is_null($this->engine->getError())) {
                throw new \Exception($this->engine->getError());
            }
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }


    /**
     * 获取错误信息
     * @return string|null
     * @author LZH
     * @date 2025/2/19
     */
    public function getError(): ?string
    {
        return $this->error;
    }


    /**
     * 发送短信
     * @param string $mobile
     * @param array $data
     * @return false
     * @author LZH
     * @date 2025/2/19
     */
    public function send(string $mobile, array $data): bool
    {
        try {
            // 发送频率限制
            $this->sendLimit($mobile);
            // 开始发送
            $result = $this->engine
                ->setMobile($mobile)
                ->setTemplateId($data['template_id'])
                ->setTemplateParams($data['params'])
                ->send();
            if(false === $result) {
                throw new \Exception($this->engine->getError());
            }
            return $result;
        } catch(\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }


    /**
     * 发送频率限制
     * @param string $mobile
     * @return void
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public function sendLimit(string $mobile): void
    {
        $smsLog = SmsLog::where([
            ['mobile', '=', $mobile],
            ['send_status', '=', SmsEnum::SEND_SUCCESS],
            ['scene_id', 'in', NoticeEnum::SMS_SCENE],
            ])
            ->order('send_time', 'desc')
            ->findOrEmpty()
            ->toArray();
        if(!empty($smsLog) && ($smsLog['send_time'] > time() - 60)) {
            throw new \Exception('同一手机号1分钟只能发送1条短信');
        }
    }

    /**
     * 校验手机验证码
     * @param string $mobile
     * @param string $code
     * @param int $sceneId
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public function verify(string $mobile, string $code, int $sceneId = 0): bool
    {
        $where = [
            ['mobile', '=', $mobile],
            ['send_status', '=', SmsEnum::SEND_SUCCESS],
            ['scene_id', 'in', NoticeEnum::SMS_SCENE],
            ['is_verify', '=', YesNoEnum::NO],
        ];

        if (!empty($sceneId)) {
            $where[] = ['scene_id', '=', $sceneId];
        }

        $smsLog = SmsLog::where($where)
            ->order('send_time', 'desc')
            ->findOrEmpty();

        // 没有验证码 或 最新验证码已校验 或 已过期(有效期：5分钟)
        if($smsLog->isEmpty() || $smsLog->is_verify || ($smsLog->send_time < time() - 5 * 60) ) {
            return false;
        }

        // 更新校验状态
        if($smsLog->code == $code) {
            $smsLog->check_num = $smsLog->check_num + 1;
            $smsLog->is_verify = YesNoEnum::YES;
            $smsLog->save();
            return true;
        }

        // 更新验证次数
        $smsLog->check_num = $smsLog->check_num + 1;
        $smsLog->save();
        return false;
    }
}