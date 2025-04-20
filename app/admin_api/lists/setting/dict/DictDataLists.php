<?php
declare(strict_types=1);

namespace app\admin_api\lists\setting\dict;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\dict\DictData;

/**
 * 字典数据列表
 * @class DictDataLists
 * @package app\admin_api\lists\setting\dict
 * @author LZH
 * @date 2025/2/19
 */
class DictDataLists extends BaseAdminDataLists implements ListsSearchInterface
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
            '%like%' => ['name', 'type_value'],
            '=' => ['status', 'type_id']
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
        return DictData::where($this->searchWhere)
            ->append(['status_desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['sort' => 'desc', 'id' => 'desc'])
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
        return DictData::where($this->searchWhere)->count();
    }

}