<?php

namespace app\front_api\logic;

use app\common\enum\notice\NoticeEnum;
use app\common\logic\BaseLogic;

/**
 * 短信逻辑
 * @class SmsLogic
 * @package app\front_api\logic
 * @author LZH
 * @date 2025/2/20
 */
class SmsLogic extends BaseLogic
{

    /**
     * 发送验证码
     * @param $params
     * @return false|mixed
     * @author LZH
     * @date 2025/2/20
     */
    public static function sendCode($params)
    {
        try {
            $scene = NoticeEnum::getSceneByTag($params['scene']);
            if (empty($scene)) {
                throw new \Exception('场景值异常');
            }

            $result = event('Notice',  [
                'scene_id' => $scene,
                'params' => [
                    'mobile' => $params['mobile'],
                    'code' => mt_rand(1000, 9999),
                ]
            ]);

            return $result[0];

        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

}