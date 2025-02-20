<?php

namespace app\front_api\controller;

use app\front_api\lists\article\ArticleCollectLists;
use app\front_api\lists\article\ArticleLists;
use app\front_api\logic\ArticleLogic;

/**
 * 文章管理
 * @class ArticleController
 * @package app\front_api\controller
 * @author LZH
 * @date 2025/2/19
 */
class ArticleController extends BaseApiController
{

    public array $notNeedLogin = ['lists', 'cate', 'detail'];

    /**
     * 文章列表
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function lists()
    {
        return $this->dataLists(new ArticleLists());
    }

    /**
     * 文章分类列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function cate()
    {
        return $this->data(ArticleLogic::cate());
    }


    /**
     * 收藏列表
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function collect()
    {
        return $this->dataLists(new ArticleCollectLists());
    }


    /**
     * 文章详情
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function detail()
    {
        $id = $this->request->get('id/d');
        $result = ArticleLogic::detail($id, $this->userId);
        return $this->data($result);
    }

    /**
     * 加入收藏
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function addCollect()
    {
        $articleId = $this->request->post('id/d');
        ArticleLogic::addCollect($articleId, $this->userId);
        return $this->success('操作成功');
    }

    /**
     * 取消收藏
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function cancelCollect()
    {
        $articleId = $this->request->post('id/d');
        ArticleLogic::cancelCollect($articleId, $this->userId);
        return $this->success('操作成功');
    }

}