<?php
declare(strict_types=1);

namespace app\front_api\controller;

use app\front_api\lists\AccountLogLists;
use think\response\Json;

/**
 * 账户流水
 * @class AccountLogController
 * @package app\front_api\controller
 * @author LZH
 * @date 2025/2/19
 */
class AccountLogController extends BaseApiController
{
    /**
     * 账户流水
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): Json
    {
        return $this->dataLists(new AccountLogLists());
    }
}