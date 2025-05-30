<?php
declare(strict_types=1);

namespace app\admin_api\controller\decorate;


use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\decorate\DecoratePageLogic;
use app\admin_api\validate\decorate\DecoratePageValidate;
use think\response\Json;


/**
 * 装修页面
 * @class PageController
 * @package app\admin_api\controller\decorate
 * @author LZH
 * @date 2025/2/20
 */
class PageController extends BaseAdminApiController
{

    /**
     * 获取装修修页面详情
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail(): Json
    {
        $id = $this->request->get('id/d');
        $result = DecoratePageLogic::getDetail($id);
        return $this->success('获取成功', $result);
    }


    /**
     * 保存装修配置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function save(): Json
    {
        $params = (new DecoratePageValidate())->post()->goCheck();
        $result = DecoratePageLogic::save($params);
        if (false === $result) {
            return $this->fail(DecoratePageLogic::getError());
        }
        return $this->success('操作成功', [], 1, 1);
    }

}