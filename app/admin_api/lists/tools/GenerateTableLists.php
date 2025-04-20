<?php
declare(strict_types=1);

namespace app\admin_api\lists\tools;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\tools\GenerateTable;

/**
 * 代码生成所选数据表列表
 * @class GenerateTableLists
 * @package app\admin_api\lists\tools
 * @author LZH
 * @date 2025/2/19
 */
class GenerateTableLists extends BaseAdminDataLists implements ListsSearchInterface
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
            '%like%' => ['table_name', 'table_comment']
        ];
    }


    /**
     * 查询列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        return GenerateTable::where($this->searchWhere)
            ->order(['id' => 'desc'])
            ->append(['template_type_desc'])
            ->limit($this->limitOffset, $this->limitLength)
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
        return GenerateTable::count();
    }

}