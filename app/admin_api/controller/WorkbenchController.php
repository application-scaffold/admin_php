<?php
declare(strict_types=1);

namespace app\admin_api\controller;

use app\admin_api\logic\WorkbenchLogic;
use think\response\Json;

class WorkbenchController extends BaseAdminApiController
{

    public function index(): Json
    {
        $result = WorkbenchLogic::index();
        return $this->data($result);
    }
}