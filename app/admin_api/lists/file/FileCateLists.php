<?php
declare(strict_types=1);

namespace app\admin_api\lists\file;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\file\FileCate;

/**
 * 文件分类列表
 * @class FileCateLists
 * @package app\admin_api\lists\file
 * @author LZH
 * @date 2025/2/19
 */
class FileCateLists extends BaseAdminDataLists implements ListsSearchInterface
{

    /**
     * 文件分类搜素条件
     * @return array[]
     * @author LZH
     * @date 2025/2/19
     */
    public function setSearch(): array
    {
        return [
            '=' => ['type']
        ];
    }


    /**
     * 获取文件分类列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $lists = (new FileCate())->field(['id,pid,type,name'])
            ->where($this->searchWhere)
            ->order('id desc')
            ->select()->toArray();

        return linear_to_tree($lists, 'children');
    }

    /**
     * 获取文件分类数量
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        return (new FileCate())->where($this->searchWhere)->count();
    }
}