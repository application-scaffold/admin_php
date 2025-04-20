<?php
declare(strict_types=1);

namespace app\admin_api\controller\article;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\article\ArticleLists;
use app\admin_api\logic\article\ArticleLogic;
use app\admin_api\validate\article\ArticleValidate;
use think\response\Json;

/**
 * 资讯管理控制器
 * @class ArticleController
 * @package app\admin_api\controller\article
 * @author LZH
 * @date 2025/2/20
 */
class ArticleController extends BaseAdminApiController
{

    /**
     * 查看资讯列表
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists(): Json
    {
        return $this->dataLists(new ArticleLists());
    }

    /**
     * 添加资讯
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function add(): Json
    {
        $params = (new ArticleValidate())->post()->goCheck('add');
        ArticleLogic::add($params);
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * 编辑资讯
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function edit(): Json
    {
        $params = (new ArticleValidate())->post()->goCheck('edit');
        $result = ArticleLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(ArticleLogic::getError());
    }

    /**
     * 删除资讯
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function delete(): Json
    {
        $params = (new ArticleValidate())->post()->goCheck('delete');
        ArticleLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * 资讯详情
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail(): Json
    {
        $params = (new ArticleValidate())->goCheck('detail');
        $result = ArticleLogic::detail($params);
        return $this->data($result);
    }


    /**
     * 更改资讯状态
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function updateStatus(): Json
    {
        $params = (new ArticleValidate())->post()->goCheck('status');
        $result = ArticleLogic::updateStatus($params);
        if (true === $result) {
            return $this->success('修改成功', [], 1, 1);
        }
        return $this->fail(ArticleLogic::getError());
    }

}