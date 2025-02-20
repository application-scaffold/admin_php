<?php

namespace app\index\controller;

use app\BaseController;
use app\common\service\JsonService;
use think\facade\Request;

class IndexController extends BaseController
{
    /**
     * @param $name
     * @return \think\response\Json|\think\response\View
     * @author LZH
     * @date 2025/2/18
     */
    public function index($name = '你好，ThinkPHP6')
    {
        $template = app()->getRootPath() . 'public/pc/index.html';
        if (Request::isMobile()) {
            $template = app()->getRootPath() . 'public/mobile/index.html';
        }

        if (file_exists($template)) {
            return view($template);
        }

        return JsonService::success();
    }
}
