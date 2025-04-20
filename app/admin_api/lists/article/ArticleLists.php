<?php
declare(strict_types=1);

namespace app\admin_api\lists\article;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\lists\ListsSortInterface;
use app\common\model\article\Article;

/**
 * 资讯列表
 * @class ArticleLists
 * @package app\admin_api\lists\article
 * @author LZH
 * @date 2025/2/19
 */
class ArticleLists extends BaseAdminDataLists implements ListsSearchInterface, ListsSortInterface
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
            '%like%' => ['title'],
            '=' => ['cid', 'is_show']
        ];
    }

    /**
     * 设置支持排序字段
     * @return string[]
     * @author LZH
     * @date 2025/2/19
     */
    public function setSortFields(): array
    {
        return ['create_time' => 'create_time', 'id' => 'id'];
    }

    /**
     * 设置默认排序
     * @return string[]
     * @author LZH
     * @date 2025/2/19
     */
    public function setDefaultOrder(): array
    {
        return ['sort' => 'desc', 'id' => 'desc'];
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
        $ArticleLists = Article::where($this->searchWhere)
            ->append(['cate_name', 'click'])
            ->limit($this->limitOffset, $this->limitLength)
            ->order($this->sortOrder)
            ->select()
            ->toArray();

        return $ArticleLists;
    }

    /**
     * 获取数量
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        return Article::where($this->searchWhere)->count();
    }

    public function extend(): array
    {
        return [];
    }
}