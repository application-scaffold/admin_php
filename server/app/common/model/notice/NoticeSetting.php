<?php

namespace app\common\model\notice;

use app\common\enum\DefaultEnum;
use app\common\enum\notice\NoticeEnum;
use app\common\model\BaseModel;

class NoticeSetting extends BaseModel
{

    /**
     * 短信通知状态
     * @param $value
     * @param $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getSmsStatusDescAttr($value,$data)
    {
        if ($data['sms_notice']) {
            $sms_text = json_decode($data['sms_notice'],true);
            return DefaultEnum::getEnableDesc($sms_text['status']);
        }else {
            return '停用';
        }
    }

    /**
     * 通知类型
     * @param $value
     * @param $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getTypeDescAttr($value,$data)
    {
        return NoticeEnum::getTypeDesc($data['type']);
    }

    /**
     * 接收者描述获取器
     * @param $value
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getRecipientDescAttr($value)
    {
        $desc = [
            1 => '买家',
            2 => '卖家',
        ];
        return $desc[$value] ?? '';
    }

    /**
     * 系统通知获取器
     * @param $value
     * @return array|mixed
     * @author LZH
     * @date 2025/2/18
     */
    public function getSystemNoticeAttr($value)
    {
        return empty($value) ? [] : json_decode($value, true);
    }

    /**
     * 短信通知获取器
     * @param $value
     * @return array|mixed
     * @author LZH
     * @date 2025/2/18
     */
    public function getSmsNoticeAttr($value)
    {
        return empty($value) ? [] : json_decode($value, true);
    }

    /**
     * 公众号通知获取器
     * @param $value
     * @return array|mixed
     * @author LZH
     * @date 2025/2/18
     */
    public function getOaNoticeAttr($value)
    {
        return empty($value) ? [] : json_decode($value, true);
    }

    /**
     * 小程序通知获取器
     * @param $value
     * @return array|mixed
     * @author LZH
     * @date 2025/2/18
     */
    public function getMnpNoticeAttr($value)
    {
        return empty($value) ? [] : json_decode($value, true);
    }
}