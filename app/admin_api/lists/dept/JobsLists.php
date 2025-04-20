<?php

namespace app\admin_api\lists\dept;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsSearchInterface;
use app\common\model\dept\Jobs;

/**
 * 岗位列表
 * @class JobsLists
 * @package app\admin_api\lists\dept
 * @author LZH
 * @date 2025/2/19
 */
class JobsLists extends BaseAdminDataLists implements ListsSearchInterface,ListsExcelInterface
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
            '%like%' => ['name'],
            '=' => ['code', 'status']
        ];
    }


    /**
     * 获取管理列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $lists = Jobs::where($this->searchWhere)
            ->append(['status_desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['sort' => 'desc', 'id' => 'desc'])
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
        return Jobs::where($this->searchWhere)->count();
    }


    /**
     * 导出文件名
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function setFileName(): string
    {
        return '岗位列表';
    }

    /**
     * 导出字段
     * @return string[]
     * @author LZH
     * @date 2025/2/19
     */
    public function setExcelFields(): array
    {
        return [
            'code' => '岗位编码',
            'name' => '岗位名称',
            'remark' => '备注',
            'status_desc' => '状态',
            'create_time' => '添加时间',
        ];
    }

}