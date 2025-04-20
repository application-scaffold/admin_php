<?php

namespace app\admin_api\lists\tools;

use app\admin_api\lists\BaseAdminDataLists;
use think\facade\Db;

/**
 * 数据表列表
 * @class DataTableLists
 * @package app\admin_api\lists\tools
 * @author LZH
 * @date 2025/2/19
 */
class DataTableLists extends BaseAdminDataLists
{

    /**
     * 查询结果
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function queryResult()
    {
        $sql = 'SHOW TABLE STATUS WHERE 1=1 ';
        if (!empty($this->params['name'])) {
            $sql .= "AND name LIKE '%" . $this->params['name'] . "%'";
        }
        if (!empty($this->params['comment'])) {
            $sql .= "AND comment LIKE '%" . $this->params['comment'] . "%'";
        }
        return Db::query($sql);
    }

    /**
     * 处理列表
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $lists = array_map("array_change_key_case", $this->queryResult());
        $offset = max(0, ($this->pageNo - 1) * $this->pageSize);
        $lists = array_slice($lists, $offset, $this->pageSize, true);
        return array_values($lists);
    }

    /**
     * 获取数量
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        return count($this->queryResult());
    }

}