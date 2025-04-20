<?php

namespace app\front_api\lists\article;

use app\front_api\lists\BaseApiDataLists;
use app\common\enum\YesNoEnum;
use app\common\lists\ListsSearchInterface;
use app\common\model\article\Article;
use app\common\model\article\ArticleCollect;

/**
 * 文章列表
 * @class ArticleLists
 * @package app\front_api\lists\article
 * @author LZH
 * @date 2025/2/19
 */
class ArticleLists extends BaseApiDataLists implements ListsSearchInterface
{

    /**
     * 搜索条件
     * @return array[]
     * @author LZH
     * @date 2025/2/19
     */
    public function setSearch(): array
    {
        return [
            '=' => ['cid']
        ];
    }

    /**
     * 自定查询条件
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function queryWhere()
    {
        $where[] = ['is_show', '=', 1];
        if (!empty($this->params['keyword'])) {
            $where[] = ['title', 'like', '%' . $this->params['keyword'] . '%'];
        }
        return $where;
    }

    /**
     * 获取文章列表
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $orderRaw = 'sort desc, id desc';
        $sortType = $this->params['sort'] ?? 'default';
        // 最新排序
        if ($sortType == 'new') {
            $orderRaw = 'id desc';
        }
        // 最热排序
        if ($sortType == 'hot') {
            $orderRaw = 'click_actual + click_virtual desc, id desc';
        }

        $field = 'id,cid,title,desc,image,click_virtual,click_actual,create_time';
        $result = Article::field($field)
            ->where($this->queryWhere())
            ->where($this->searchWhere)
            ->orderRaw($orderRaw)
            ->append(['click'])
            ->hidden(['click_virtual', 'click_actual'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()->toArray();

        $articleIds = array_column($result, 'id');

        $collectIds = ArticleCollect::where(['user_id' => $this->userId, 'status' => YesNoEnum::YES])
            ->whereIn('article_id', $articleIds)
            ->column('article_id');

        foreach ($result as &$item) {
            $item['collect'] = in_array($item['id'], $collectIds);
        }

        return $result;
    }

    /**
     * 获取文章数量
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        return Article::where($this->searchWhere)
            ->where($this->queryWhere())
            ->count();
    }
}