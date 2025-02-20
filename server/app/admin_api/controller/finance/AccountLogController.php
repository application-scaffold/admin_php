<?php

namespace app\admin_api\controller\finance;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\finance\AccountLogLists;
use app\common\enum\user\AccountLogEnum;

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
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists()
    {
        return $this->dataLists(new AccountLogLists());
    }

    /**
     * 用户余额变动类型
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getUmChangeType()
    {
        return $this->data(AccountLogEnum::getUserMoneyChangeTypeDesc());
    }

}