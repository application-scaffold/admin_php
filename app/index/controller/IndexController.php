<?php
declare(strict_types=1);

namespace app\index\controller;

use app\BaseController;
use app\common\service\JsonService;
use think\facade\Request;
use think\response\Json;
use think\response\View;

class IndexController extends BaseController
{
    /**
     * @param string $name
     * @return Json|View
     * @author LZH
     * @date 2025/2/18
     */
    public function index(string $name = '你好，ThinkPHP6'): View|Json
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
