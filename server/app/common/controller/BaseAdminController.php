<?php

declare(strict_types=1);

namespace app\common\controller;

use app\BaseController;
use app\common\lists\BaseDataLists;
use app\common\service\JsonService;
use think\facade\App;

/**
 * 控制器基类
 * @class BaseAdminController
 * @package app\common\controller
 * @author LZH
 * @date 2025/2/18
 */
class BaseAdminController extends BaseController
{
    // 不需要登录验证的方法名数组
    public array $notNeedLogin = [];

    /**
     * 操作成功
     * @param string $msg 成功消息
     * @param array $data 返回的数据
     * @param int $code 状态码
     * @param int $show 是否显示消息
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/18
     */
    protected function success(string $msg = 'success', array $data = [], int $code = 1, int $show = 0)
    {
        return JsonService::success($msg, $data, $code, $show);
    }

    /**
     * 数据返回
     * @param $data 返回的数据
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/18
     */
    protected function data($data)
    {
        return JsonService::data($data);
    }

    /**
     * 列表数据返回
     * @param BaseDataLists|null $lists 列表数据对象
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/18
     */
    protected function dataLists(BaseDataLists $lists = null)
    {
        // 列表类和控制器一一对应，"app/应用/controller/控制器的方法" =》"app\应用\lists\"目录下
        // 当对象为空时，自动创建列表对象
        if (is_null($lists)) {
            $listName = str_replace('.', '\\', App::getNamespace() . '\\lists\\' . $this->request->controller() . ucwords($this->request->action()));
            $lists = invoke($listName);
        }
        return JsonService::dataLists($lists);
    }

    /**
     * 操作失败
     * @param string $msg 失败消息
     * @param array $data 返回的数据
     * @param int $code 状态码
     * @param int $show 是否显示消息
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/18
     */
    protected function fail(string $msg = 'fail', array $data = [], int $code = 0, int $show = 1)
    {
        return JsonService::fail($msg, $data, $code, $show);
    }

    /**
     * 是否免登录验证
     * @return bool
     * @author LZH
     * @date 2025/2/18
     */
    public function isNotNeedLogin() : bool
    {
        $notNeedLogin = $this->notNeedLogin;
        if (empty($notNeedLogin)) {
            return false;
        }
        $action = $this->request->action();

        if (!in_array(trim($action), $notNeedLogin)) {
            return false;
        }
        return true;
    }
}
