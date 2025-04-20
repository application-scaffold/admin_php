<?php
declare(strict_types=1);

namespace app\admin_api\controller\setting\pay;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\setting\pay\PayConfigLists;
use app\admin_api\logic\setting\pay\PayConfigLogic;
use app\admin_api\validate\setting\PayConfigValidate;
use think\response\Json;

/**
 * 支付配置
 * @class PayConfigController
 * @package app\admin_api\controller\setting\pay
 * @author LZH
 * @date 2025/2/20
 */
class PayConfigController extends BaseAdminApiController
{

    /**
     * 设置支付配置
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function setConfig(): Json
    {
        $params = (new PayConfigValidate())->post()->goCheck();
        PayConfigLogic::setConfig($params);
        return $this->success('设置成功', [], 1, 1);
    }

    /**
     * 获取支付配置
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function getConfig(): Json
    {
        $id = (new PayConfigValidate())->goCheck('get');
        $result = PayConfigLogic::getConfig($id);
        return $this->success('获取成功', $result);
    }


    /**
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists(): Json
    {
        return $this->dataLists(new PayConfigLists());
    }
}