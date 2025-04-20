<?php
declare(strict_types=1);

namespace app\admin_api\controller\setting;


use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\setting\TransactionSettingsLogic;
use app\admin_api\validate\setting\TransactionSettingsValidate;
use think\response\Json;

/**
 * 交易设置
 * @class TransactionSettingsController
 * @package app\admin_api\controller\setting
 * @author LZH
 * @date 2025/2/20
 */
class TransactionSettingsController extends BaseAdminApiController
{

    /**
     * 获取交易设置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getConfig(): Json
    {
        $result = TransactionSettingsLogic::getConfig();
        return $this->data($result);
    }

    /**
     * 设置交易设置
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setConfig(): Json
    {
        $params = (new TransactionSettingsValidate())->post()->goCheck('setConfig');
        TransactionSettingsLogic::setConfig($params);
        return $this->success('操作成功',[],1,1);
    }
}