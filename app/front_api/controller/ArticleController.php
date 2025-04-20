<?php
declare(strict_types=1);

namespace app\front_api\controller;

use app\front_api\lists\article\ArticleCollectLists;
use app\front_api\lists\article\ArticleLists;
use app\front_api\logic\ArticleLogic;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\response\Json;

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
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): Json
    {
        return $this->dataLists(new ArticleLists());
    }

    /**
     * 文章分类列表
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function cate(): Json
    {
        return $this->data(ArticleLogic::cate());
    }


    /**
     * 收藏列表
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function collect(): Json
    {
        return $this->dataLists(new ArticleCollectLists());
    }


    /**
     * 文章详情
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function detail(): Json
    {
        $id = $this->request->get('id/d');
        $result = ArticleLogic::detail($id, $this->userId);
        return $this->data($result);
    }

    /**
     * 加入收藏
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function addCollect(): Json
    {
        $articleId = $this->request->post('id/d');
        ArticleLogic::addCollect($articleId, $this->userId);
        return $this->success('操作成功');
    }

    /**
     * 取消收藏
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function cancelCollect(): Json
    {
        $articleId = $this->request->post('id/d');
        ArticleLogic::cancelCollect($articleId, $this->userId);
        return $this->success('操作成功');
    }

}