<?php

namespace app\common\model\pay;

use app\common\enum\PayEnum;
use app\common\model\BaseModel;
use app\common\service\FileService;


class PayConfig extends BaseModel
{
    protected $name = 'dev_pay_config';

    // 设置json类型字段
    protected $json = ['config'];

    // 设置JSON数据返回数组
    protected $jsonAssoc = true;

    /**
     * 支付图标获取器 - 路径添加域名
     * @param $value
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getIconAttr($value)
    {
        return empty($value) ? '' : FileService::getFileUrl($value);
    }

    /**
     * 支付方式名称获取器
     * @param $value
     * @param $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getPayWayNameAttr($value,$data)
    {
        return PayEnum::getPayDesc($data['pay_way']);
    }
}