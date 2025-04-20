<?php
declare (strict_types = 1);

namespace app\common\model\pay;


use app\common\model\BaseModel;
use app\common\service\FileService;
use think\model\relation\HasOne;

class PayWay extends BaseModel
{
    protected $name = 'dev_pay_way';

    public function getIconAttr(string $value, array $data): string
    {
        return FileService::getFileUrl($value);
    }

    /**
     * 支付方式名称获取器
     * @param mixed $value
     * @param array $data
     * @return mixed
     * @author LZH
     * @date 2025/2/18
     */
    public static function getPayWayNameAttr(mixed $value, array $data): mixed
    {
        return PayConfig::where('id',$data['pay_config_id'])->value('name');
    }

    /**
     * 关联支配配置模型
     * @return HasOne
     * @author LZH
     * @date 2025/2/18
     */
    public function payConfig(): HasOne
    {
        return $this->hasOne(PayConfig::class,'id','pay_config_id');
    }
}