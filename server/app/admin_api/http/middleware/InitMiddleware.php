<?php

declare (strict_types=1);

namespace app\admin_api\http\middleware;

use app\admin_api\controller\BaseAdminApiController;
use app\common\exception\ControllerExtendException;
use think\exception\ClassNotFoundException;
use think\exception\HttpException;

/**
 * 初始化验证中间件
 * @class InitMiddleware
 * @package app\admin_api\http\middleware
 * @author LZH
 * @date 2025/2/19
 */
class InitMiddleware
{

    /**
     * 初始化
     * @param $request
     * @param \Closure $next
     * @return mixed
     * @throws ControllerExtendException
     * @author LZH
     * @date 2025/2/19
     */
    public function handle($request, \Closure $next)
    {
        //获取控制器
        try {
            $controller = str_replace('.', '\\', $request->controller());
            $controller = '\\app\\admin_api\\controller\\' . $controller . 'Controller';
            $controllerClass = invoke($controller);
            if (($controllerClass instanceof BaseAdminApiController) === false) {
                throw new ControllerExtendException($controller, '404');
            }
        } catch (ClassNotFoundException $e) {
            throw new HttpException(404, 'controller not exists:' . $e->getClass());
        }

        //创建控制器对象
        $request->controllerObject = invoke($controller);

        return $next($request);
    }
}