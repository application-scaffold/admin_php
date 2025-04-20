<?php
declare(strict_types=1);

namespace app\admin_api\controller\setting;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\setting\HotSearchLogic;
use think\response\Json;

/**
 * 热门搜索设置
 * @class HotSearchController
 * @package app\admin_api\controller\setting
 * @author LZH
 * @date 2025/2/20
 */
class HotSearchController extends BaseAdminApiController
{

    /**
     * 获取热门搜索
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function getConfig(): Json
    {
        $result = HotSearchLogic::getConfig();
        return $this->data($result);
    }

    /**
     * 设置热门搜索
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setConfig(): Json
    {
        $params = $this->request->post();
        $result = HotSearchLogic::setConfig($params);
        if (false === $result) {
            return $this->fail(HotSearchLogic::getError() ?: '系统错误');
        }
        return $this->success('设置成功', [], 1, 1);
    }
}