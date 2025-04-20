<?php
declare(strict_types=1);

namespace app\admin_api\controller\finance;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\finance\AccountLogLists;
use app\common\enum\user\AccountLogEnum;
use think\response\Json;

/**
 * 账户流水控制器
 * @class AccountLogController
 * @package app\admin_api\controller\finance
 * @author LZH
 * @date 2025/2/20
 */
class AccountLogController extends BaseAdminApiController
{

    /**
     * 账户流水明细
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists(): Json
    {
        return $this->dataLists(new AccountLogLists());
    }

    /**
     * 用户余额变动类型
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getUmChangeType(): Json
    {
        return $this->data(AccountLogEnum::getUserMoneyChangeTypeDesc());
    }

}