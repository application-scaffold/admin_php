<?php
declare(strict_types=1);

namespace app\admin_api\controller\setting;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\logic\setting\StorageLogic;
use app\admin_api\validate\setting\StorageValidate;
use think\response\Json;

/**
 * 存储设置控制器
 * @class StorageController
 * @package app\admin_api\controller\setting
 * @author LZH
 * @date 2025/2/20
 */
class StorageController extends BaseAdminApiController
{

    /**
     * 获取存储引擎列表
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists(): Json
    {
        return $this->success('获取成功', StorageLogic::lists());
    }


    /**
     * 存储配置信息
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail(): Json
    {
        $param = (new StorageValidate())->get()->goCheck('detail');
        return $this->success('获取成功', StorageLogic::detail($param));
    }


    /**
     * 设置存储参数
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function setup(): Json
    {
        $params = (new StorageValidate())->post()->goCheck('setup');
        $result = StorageLogic::setup($params);
        if (true === $result) {
            return $this->success('配置成功', [], 1, 1);
        }
        return $this->success($result, [], 1, 1);
    }


    /**
     * 切换存储引擎
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function change(): Json
    {
        $params = (new StorageValidate())->post()->goCheck('change');
        StorageLogic::change($params);
        return $this->success('切换成功', [], 1, 1);
    }
}
