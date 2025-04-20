<?php

namespace app\front_api\logic;

use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\article\Article;
use app\common\model\article\ArticleCate;
use app\common\model\article\ArticleCollect;

/**
 * 文章逻辑
 * @class ArticleLogic
 * @package app\front_api\logic
 * @author LZH
 * @date 2025/2/19
 */
class ArticleLogic extends BaseLogic
{

    /**
     * 文章详情
     * @param $articleId
     * @param $userId
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function detail($articleId, $userId)
    {
        // 文章详情
        $article = Article::getArticleDetailArr($articleId);
        // 关注状态
        $article['collect'] = ArticleCollect::isCollectArticle($userId, $articleId);

        return $article;
    }

    /**
     * 加入收藏
     * @param $articleId
     * @param $userId
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function addCollect($articleId, $userId)
    {
        $where = ['user_id' => $userId, 'article_id' => $articleId];
        $collect = ArticleCollect::where($where)->findOrEmpty();
        if ($collect->isEmpty()) {
            ArticleCollect::create([
                'user_id' => $userId,
                'article_id' => $articleId,
                'status' => YesNoEnum::YES
            ]);
        } else {
            ArticleCollect::update([
                'id' => $collect['id'],
                'status' => YesNoEnum::YES
            ]);
        }
    }

    /**
     * 取消收藏
     * @param $articleId
     * @param $userId
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function cancelCollect($articleId, $userId)
    {
        ArticleCollect::update(['status' => YesNoEnum::NO], [
            'user_id' => $userId,
            'article_id' => $articleId,
            'status' => YesNoEnum::YES
        ]);
    }

    /**
     * 文章分类
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function cate()
    {
        return ArticleCate::field('id,name')
            ->where('is_show', '=', 1)
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()->toArray();
    }

}