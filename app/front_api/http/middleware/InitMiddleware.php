<?php

declare (strict_types=1);

namespace app\front_api\http\middleware;

use app\common\exception\ControllerExtendException;
use app\front_api\controller\BaseApiController;
use think\exception\ClassNotFoundException;
use think\exception\HttpException;

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
            $controller = '\\app\\front_api\\controller\\' . $controller . 'Controller';
            $controllerClass = invoke($controller);
            if (($controllerClass instanceof BaseApiController) === false) {
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