<?php
declare(strict_types=1);

namespace app\admin_api\controller\channel;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\channel\OfficialAccountReplyLists;
use app\admin_api\logic\channel\OfficialAccountReplyLogic;
use app\admin_api\validate\channel\OfficialAccountReplyValidate;
use think\Response;
use think\response\Json;

/**
 * 微信公众号回复控制器
 * @class OfficialAccountReplyController
 * @package app\admin_api\controller\channel
 * @author LZH
 * @date 2025/2/20
 */
class OfficialAccountReplyController extends BaseAdminApiController
{

    public array $notNeedLogin = ['index'];

    /**
     * 查看回复列表(关注/关键词/默认)
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists(): Json
    {
        return $this->dataLists(new OfficialAccountReplyLists());
    }

    /**
     * 添加回复(关注/关键词/默认)
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function add(): Json
    {
        $params = (new OfficialAccountReplyValidate())->post()->goCheck('add');
        $result = OfficialAccountReplyLogic::add($params);
        if ($result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(OfficialAccountReplyLogic::getError());
    }

    /**
     * 查看回复详情
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail(): Json
    {
        $params = (new OfficialAccountReplyValidate())->goCheck('detail');
        $result = OfficialAccountReplyLogic::detail($params);
        return $this->data($result);
    }

    /**
     * 编辑回复(关注/关键词/默认)
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function edit(): Json
    {
        $params = (new OfficialAccountReplyValidate())->post()->goCheck('edit');
        $result = OfficialAccountReplyLogic::edit($params);
        if ($result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(OfficialAccountReplyLogic::getError());
    }

    /**
     * 删除回复(关注/关键词/默认)
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function delete(): Json
    {
        $params = (new OfficialAccountReplyValidate())->post()->goCheck('delete');
        OfficialAccountReplyLogic::delete($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * 更新排序
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function sort(): Json
    {
        $params = (new OfficialAccountReplyValidate())->post()->goCheck('sort');
        OfficialAccountReplyLogic::sort($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * 更新状态
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function status(): Json
    {
        $params = (new OfficialAccountReplyValidate())->post()->goCheck('status');
        OfficialAccountReplyLogic::status($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * 微信公众号回调
     * @return Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function index(): Response
    {
        $result = OfficialAccountReplyLogic::index();
        return response($result->getBody())->header([
            'Content-Type' => 'text/plain;charset=utf-8'
        ]);
    }
}