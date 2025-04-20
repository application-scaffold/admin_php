<?php
declare(strict_types=1);

namespace app\front_api\lists\article;

use app\front_api\lists\BaseApiDataLists;
use app\common\enum\YesNoEnum;
use app\common\model\article\Article;

/**
 * 文章收藏列表
 * @class ArticleCollectLists
 * @package app\front_api\lists\article
 * @author LZH
 * @date 2025/2/19
 */
class ArticleCollectLists extends BaseApiDataLists
{

    /**
     * 获取收藏列表
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $field = "c.id,c.article_id,a.title,a.image,a.desc,a.is_show,
        a.click_virtual, a.click_actual,a.create_time, c.create_time as collect_time";

        $lists = (new Article())->alias('a')
            ->join('article_collect c', 'c.article_id = a.id')
            ->field($field)
            ->where([
                'c.user_id' => $this->userId,
                'c.status' => YesNoEnum::YES,
                'a.is_show' => YesNoEnum::YES,
            ])
            ->order(['sort' => 'desc', 'c.id' => 'desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->append(['click'])
            ->hidden(['click_virtual', 'click_actual'])
            ->select()->toArray();

        foreach ($lists as &$item) {
            $item['collect_time'] = date('Y-m-d H:i', $item['collect_time']);
        }

        return $lists;
    }

    /**
     * 获取收藏数量
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        return (new Article())->alias('a')
            ->join('article_collect c', 'c.article_id = a.id')
            ->where([
                'c.user_id' => $this->userId,
                'c.status' => YesNoEnum::YES,
                'a.is_show' => YesNoEnum::YES,
            ])
            ->count();
    }
}