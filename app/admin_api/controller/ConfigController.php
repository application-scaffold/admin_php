<?php
declare(strict_types=1);

namespace app\admin_api\controller;

use app\admin_api\logic\auth\AuthLogic;
use app\admin_api\logic\ConfigLogic;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\response\Json;

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
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getConfig(): Json
    {
        $data = ConfigLogic::getConfig();
        return $this->data($data);
    }

    /**
     * 根据类型获取字典数据
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function dict(): Json
    {
        $type = $this->request->get('type', '');
        $data = ConfigLogic::getDictByType($type);
        return $this->data($data);
    }

}