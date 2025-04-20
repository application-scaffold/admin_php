<?php
declare(strict_types=1);

namespace app\admin_api\lists\setting\dict;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\dict\DictType;


/**
 * 字典类型列表
 * @class DictTypeLists
 * @package app\admin_api\lists\setting\dict
 * @author LZH
 * @date 2025/2/19
 */
class DictTypeLists extends BaseAdminDataLists implements ListsSearchInterface
{

    /**
     * 设置搜索条件
     * @return array[]
     * @author LZH
     * @date 2025/2/19
     */
    public function setSearch(): array
    {
        return [
            '%like%' => ['name', 'type'],
            '=' => ['status']
        ];
    }

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
        return DictType::where($this->searchWhere)
            ->limit($this->limitOffset, $this->limitLength)
            ->append(['status_desc'])
            ->order(['id' => 'desc'])
            ->select()
            ->toArray();
    }

    /**
     * 获取数量
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        return DictType::where($this->searchWhere)->count();
    }

}