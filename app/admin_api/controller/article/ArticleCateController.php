<?php
declare(strict_types=1);

namespace app\admin_api\controller\article;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\article\ArticleCateLists;
use app\admin_api\logic\article\ArticleCateLogic;
use app\admin_api\validate\article\ArticleCateValidate;
use think\response\Json;

/**
 * 资讯分类管理控制器
 * @class ArticleCateController
 * @package app\admin_api\controller\article
 * @author LZH
 * @date 2025/2/20
 */
class ArticleCateController extends BaseAdminApiController
{

    /**
     * 查看资讯分类列表
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists(): Json
    {
        return $this->dataLists(new ArticleCateLists());
    }

    /**
     * 添加资讯分类
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function add(): Json
    {
        $params = (new ArticleCateValidate())->post()->goCheck('add');
        ArticleCateLogic::add($params);
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * 编辑资讯分类
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function edit(): Json
    {
        $params = (new ArticleCateValidate())->post()->goCheck('edit');
        $result = ArticleCateLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(ArticleCateLogic::getError());
    }

    /**
     * 删除资讯分类
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function delete(): Json
    {
        $params = (new ArticleCateValidate())->post()->goCheck('delete');
        ArticleCateLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * 资讯分类详情
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail(): Json
    {
        $params = (new ArticleCateValidate())->goCheck('detail');
        $result = ArticleCateLogic::detail($params);
        return $this->data($result);
    }


    /**
     * 更改资讯分类状态
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function updateStatus(): Json
    {
        $params = (new ArticleCateValidate())->post()->goCheck('status');
        $result = ArticleCateLogic::updateStatus($params);
        if (true === $result) {
            return $this->success('修改成功', [], 1, 1);
        }
        return $this->fail(ArticleCateLogic::getError());
    }


    /**
     * 获取文章分类
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function all(): Json
    {
        $result = ArticleCateLogic::getAllData();
        return $this->data($result);
    }

}