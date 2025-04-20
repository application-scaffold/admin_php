<?php
declare(strict_types=1);

namespace app\admin_api\lists\setting\pay;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\model\pay\PayConfig;

/**
 * 支付配置列表
 * @class PayConfigLists
 * @package app\admin_api\lists\setting\pay
 * @author LZH
 * @date 2025/2/19
 */
class PayConfigLists extends BaseAdminDataLists
{

    /**
     * 获取列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $lists = PayConfig::field('id,name,pay_way,icon,sort')
            ->append(['pay_way_name'])
            ->order('sort','desc')
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * 获取数量
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        return PayConfig::count();
    }

}