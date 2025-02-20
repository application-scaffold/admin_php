<?php

namespace app\admin_api\controller;

use app\admin_api\logic\auth\AuthLogic;
use app\admin_api\logic\ConfigLogic;

/**
 * 配置控制器
 * @class ConfigController
 * @package app\admin_api\controller
 * @author LZH
 * @date 2025/2/20
 */
class ConfigController extends BaseAdminApiController
{
    public array $notNeedLogin = ['getConfig', 'dict'];

    /**
     * 基础配置
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getConfig()
    {
        $data = ConfigLogic::getConfig();
        return $this->data($data);
    }


    /**
     * 根据类型获取字典数据
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function dict()
    {
        $type = $this->request->get('type', '');
        $data = ConfigLogic::getDictByType($type);
        return $this->data($data);
    }

}