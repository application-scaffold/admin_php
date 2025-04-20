<?php
declare(strict_types=1);

namespace app\front_api\controller;

use app\common\controller\BaseAdminController;

class BaseApiController extends BaseAdminController
{
    protected int $userId = 0;
    protected array $userInfo = [];

    public function initialize(): void
    {
        if (isset($this->request->userInfo) && $this->request->userInfo) {
            $this->userInfo = $this->request->userInfo;
            $this->userId = $this->request->userInfo['user_id'];
        }
    }
}