<?php

namespace app\admin_api\controller\setting;


use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\setting\TransactionSettingsLogic;
use app\admin_api\validate\setting\TransactionSettingsValidate;

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
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getConfig()
    {
        $result = TransactionSettingsLogic::getConfig();
        return $this->data($result);
    }

    /**
     * 设置交易设置
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setConfig()
    {
        $params = (new TransactionSettingsValidate())->post()->goCheck('setConfig');
        TransactionSettingsLogic::setConfig($params);
        return $this->success('操作成功',[],1,1);
    }
}