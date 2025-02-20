<?php

namespace app\common\model\pay;


use app\common\model\BaseModel;
use app\common\service\FileService;


class PayWay extends BaseModel
{
    protected $name = 'dev_pay_way';

    public function getIconAttr($value,$data)
    {
        return FileService::getFileUrl($value);
    }

    /**
     * 支付方式名称获取器
     * @param $value
     * @param $data
     * @return mixed
     * @author LZH
     * @date 2025/2/18
     */
    public static function getPayWayNameAttr($value,$data)
    {
        return PayConfig::where('id',$data['pay_config_id'])->value('name');
    }

    /**
     * 关联支配配置模型
     * @return \think\model\relation\HasOne
     * @author LZH
     * @date 2025/2/18
     */
    public function payConfig()
    {
        return $this->hasOne(PayConfig::class,'id','pay_config_id');
    }
}