<?php
declare(strict_types=1);

namespace app\admin_api\logic\notice;

use app\common\enum\notice\NoticeEnum;
use app\common\logic\BaseLogic;
use app\common\model\notice\NoticeSetting;

/**
 * 通知逻辑层
 * @class NoticeLogic
 * @package app\admin_api\logic\notice
 * @author LZH
 * @date 2025/2/19
 */
class NoticeLogic extends BaseLogic
{

    /**
     * 查看通知设置详情
     * @param array $params
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function detail(array $params): array
    {
        $field = 'id,type,scene_id,scene_name,scene_desc,system_notice,sms_notice,oa_notice,mnp_notice,support';
        $noticeSetting = NoticeSetting::field($field)->findOrEmpty($params['id'])->toArray();
        if (empty($noticeSetting)) {
            return [];
        }
        if (empty($noticeSetting['system_notice'])) {
            $noticeSetting['system_notice'] = [
                'title' => '',
                'content' => '',
                'status' => 0,
            ];
        }
        $noticeSetting['system_notice']['tips'] = NoticeEnum::getOperationTips(NoticeEnum::SYSTEM, $noticeSetting['scene_id']);
        if (empty($noticeSetting['sms_notice'])) {
            $noticeSetting['sms_notice'] = [
                'template_id' => '',
                'content' => '',
                'status' => 0,
            ];
        }
        $noticeSetting['sms_notice']['tips'] = NoticeEnum::getOperationTips(NoticeEnum::SMS, $noticeSetting['scene_id']);
        if (empty($noticeSetting['oa_notice'])) {
            $noticeSetting['oa_notice'] = [
                'template_id' => '',
                'template_sn' => '',
                'name' => '',
                'first' => '',
                'remark' => '',
                'tpl' => [],
                'status' => 0,
            ];
        }
        $noticeSetting['oa_notice']['tips'] = NoticeEnum::getOperationTips(NoticeEnum::MNP, $noticeSetting['scene_id']);
        if (empty($noticeSetting['mnp_notice'])) {
            $noticeSetting['mnp_notice'] = [
                'template_id' => '',
                'template_sn' => '',
                'name' => '',
                'tpl' => [],
                'status' => 0,
            ];
        }
        $noticeSetting['mnp_notice']['tips'] = NoticeEnum::getOperationTips(NoticeEnum::MNP, $noticeSetting['scene_id']);
        $noticeSetting['system_notice']['is_show'] = in_array(NoticeEnum::SYSTEM, explode(',', $noticeSetting['support']));
        $noticeSetting['sms_notice']['is_show'] = in_array(NoticeEnum::SMS, explode(',', $noticeSetting['support']));
        $noticeSetting['oa_notice']['is_show'] = in_array(NoticeEnum::OA, explode(',', $noticeSetting['support']));
        $noticeSetting['mnp_notice']['is_show'] = in_array(NoticeEnum::MNP, explode(',', $noticeSetting['support']));
        $noticeSetting['default'] = '';
        $noticeSetting['type'] = NoticeEnum::getTypeDesc($noticeSetting['type']);
        return $noticeSetting;
    }


    /**
     * 通知设置
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function set(array $params): bool
    {
        try {
            // 校验参数
            self::checkSet($params);
            // 拼装更新数据
            $updateData = [];
            foreach ($params['template'] as $item) {
                $updateData[$item['type'] . '_notice'] = json_encode($item, JSON_UNESCAPED_UNICODE);
            }
            // 更新通知设置
            NoticeSetting::where('id', $params['id'])->update($updateData);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 校验参数
     * @param array $params
     * @return void
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public static function checkSet(array $params): void
    {
        $noticeSetting = NoticeSetting::findOrEmpty($params['id'] ?? 0);

        if ($noticeSetting->isEmpty()) {
            throw new \Exception('通知配置不存在');
        }

        if (!isset($params['template']) || !is_array($params['template']) || count($params['template']) == 0) {
            throw new \Exception('模板配置不存在或格式错误');
        }

        // 通知类型
        $noticeType = ['system', 'sms', 'oa', 'mnp'];

        foreach ($params['template'] as $item) {
            if (!is_array($item)) {
                throw new \Exception('模板项格式错误');
            }

            if (!isset($item['type']) || !in_array($item['type'], $noticeType)) {
                throw new \Exception('模板项缺少模板类型或模板类型有误');
            }

            switch ($item['type']) {
                case "system";
                    self::checkSystem($item);
                    break;
                case "sms";
                    self::checkSms($item);
                    break;
                case "oa";
                    self::checkOa($item);
                    break;
                case "mnp";
                    self::checkMnp($item);
                    break;
            }
        }
    }

    /**
     * 校验系统通知参数
     * @param array $item
     * @return void
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public static function checkSystem(array $item): void
    {
        if (!isset($item['title']) || !isset($item['content']) || !isset($item['status'])) {
            throw new \Exception('系统通知必填参数：title、content、status');
        }
    }

    /**
     * 校验短信通知必填参数
     * @param array $item
     * @return void
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public static function checkSms(array $item): void
    {
        if (!isset($item['template_id']) || !isset($item['content']) || !isset($item['status'])) {
            throw new \Exception('短信通知必填参数：template_id、content、status');
        }
    }

    /**
     * 校验微信模板消息参数
     * @param array $item
     * @return void
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public static function checkOa(array $item): void
    {
        if (!isset($item['template_id']) || !isset($item['template_sn']) || !isset($item['name']) || !isset($item['first']) || !isset($item['remark']) || !isset($item['tpl']) || !isset($item['status'])) {
            throw new \Exception('微信模板消息必填参数：template_id、template_sn、name、first、remark、tpl、status');
        }
    }

    /**
     * 校验微信小程序提醒必填参数
     * @param array $item
     * @return void
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public static function checkMnp(array $item): void
    {
        if (!isset($item['template_id']) || !isset($item['template_sn']) || !isset($item['name']) || !isset($item['tpl']) || !isset($item['status'])) {
            throw new \Exception('微信模板消息必填参数：template_id、template_sn、name、tpl、status');
        }
    }
}