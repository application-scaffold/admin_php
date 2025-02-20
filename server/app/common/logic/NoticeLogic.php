<?php

namespace app\common\logic;

use app\common\enum\notice\NoticeEnum;
use app\common\enum\YesNoEnum;
use app\common\model\notice\NoticeRecord;
use app\common\model\notice\NoticeSetting;
use app\common\model\user\User;
use app\common\service\sms\SmsMessageService;

/**
 * 通知逻辑层
 * @class NoticeLogic
 * @package app\common\logic
 * @author LZH
 * @date 2025/2/18
 */
class NoticeLogic extends BaseLogic
{

    /**
     * 根据场景发送短信
     * @param $params
     * @return bool
     * @author LZH
     * @date 2025/2/18
     */
    public static function noticeByScene($params)
    {
        try {
            $noticeSetting = NoticeSetting::where('scene_id', $params['scene_id'])->findOrEmpty()->toArray();
            if (empty($noticeSetting)) {
                throw new \Exception('找不到对应场景的配置');
            }
            // 合并额外参数
            $params = self::mergeParams($params);
            $res = false;
            self::setError('发送通知失败');

            // 短信通知
            if (isset($noticeSetting['sms_notice']['status']) && $noticeSetting['sms_notice']['status'] == YesNoEnum::YES) {
                $res = (new SmsMessageService())->send($params);
            }

            return $res;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * 整理参数
     * @param $params
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public static function mergeParams($params)
    {
        // 用户相关
        if (!empty($params['params']['user_id'])) {
            $user = User::findOrEmpty($params['params']['user_id'])->toArray();
            $params['params']['nickname'] = $user['nickname'];
            $params['params']['user_name'] = $user['nickname'];
            $params['params']['user_sn'] = $user['sn'];
            $params['params']['mobile'] = $params['params']['mobile'] ?? $user['mobile'];
        }

        // 跳转路径
        $jumpPath = self::getPathByScene($params['scene_id'], $params['params']['order_id'] ?? 0);
        $params['url'] = $jumpPath['url'];
        $params['page'] = $jumpPath['page'];

        return $params;
    }


    /**
     * 根据场景获取跳转链接
     * @param $sceneId
     * @param $extraId
     * @return string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function getPathByScene($sceneId, $extraId)
    {
        // 小程序主页路径
        $page = '/pages/index/index';
        // 公众号主页路径
        $url = '/mobile/pages/index/index';
        return [
            'url' => $url,
            'page' => $page,
        ];
    }


    /**
     * 替换消息内容中的变量占位符
     * @param $content
     * @param $params
     * @return array|mixed|string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public static function contentFormat($content, $params)
    {
        foreach ($params['params'] as $k => $v) {
            $search = '{' . $k . '}';
            $content = str_replace($search, $v, $content);
        }
        return $content;
    }


    /**
     * 添加通知记录
     * @param $params
     * @param $noticeSetting
     * @param $sendType
     * @param $content
     * @param $extra
     * @return NoticeRecord|\think\Model
     * @author LZH
     * @date 2025/2/18
     */
    public static function addNotice($params, $noticeSetting, $sendType, $content, $extra = '')
    {
        return NoticeRecord::create([
            'user_id' => $params['params']['user_id'] ?? 0,
            'title' => self::getTitleByScene($sendType, $noticeSetting),
            'content' => $content,
            'scene_id' => $noticeSetting['scene_id'],
            'read' => YesNoEnum::NO,
            'recipient' => $noticeSetting['recipient'],
            'send_type' => $sendType,
            'notice_type' => $noticeSetting['type'],
            'extra' => $extra,
        ]);
    }

    /**
     * 通知记录标题
     * @param $sendType
     * @param $noticeSetting
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public static function getTitleByScene($sendType, $noticeSetting)
    {
        switch ($sendType) {
            case NoticeEnum::SMS:
                $title = '';
                break;
            case NoticeEnum::OA:
                $title = $noticeSetting['oa_notice']['name'] ?? '';
                break;
            case NoticeEnum::MNP:
                $title = $noticeSetting['mnp_notice']['name'] ?? '';
                break;
            default:
                $title = '';
        }
        return $title;
    }

}