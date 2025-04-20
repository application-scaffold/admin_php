<?php
declare (strict_types = 1);

namespace app\common\model\pay;

use app\common\enum\PayEnum;
use app\common\model\BaseModel;
use app\common\service\FileService;


class PayConfig extends BaseModel
{
    protected $name = 'dev_pay_config';

    // 设置json类型字段
    protected array $json = ['config'];

    // 设置JSON数据返回数组
    protected bool $jsonAssoc = true;

    /**
     * 支付图标获取器 - 路径添加域名
     * @param string $value
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getIconAttr(string $value): string
    {
        return empty($value) ? '' : FileService::getFileUrl($value);
    }

    /**
     * 支付方式名称获取器
     * @param mixed $value
     * @param array $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getPayWayNameAttr(mixed $value, array $data): array|string
    {
        return PayEnum::getPayDesc($data['pay_way']);
    }
}