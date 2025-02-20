<?php

namespace app\common\model\article;

use app\common\enum\YesNoEnum;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 资讯收藏
 * @class ArticleCollect
 * @package app\common\model\article
 * @author LZH
 * @date 2025/2/18
 */
class ArticleCollect extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';


    /**
     * 是否已收藏文章
     * @param $userId
     * @param $articleId
     * @return bool
     * @author LZH
     * @date 2025/2/18
     */
    public static function isCollectArticle($userId, $articleId)
    {
        $collect = ArticleCollect::where([
            'user_id' => $userId,
            'article_id' => $articleId,
            'status' => YesNoEnum::YES
        ])->findOrEmpty();

        return !$collect->isEmpty();
    }

}