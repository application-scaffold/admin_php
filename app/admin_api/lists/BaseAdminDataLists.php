<?php
declare(strict_types=1);

namespace app\admin_api\lists;

use app\common\lists\BaseDataLists;

/**
 * 管理员模块数据列表基类
 * @class BaseAdminDataLists
 * @package app\admin_api\lists
 * @author LZH
 * @date 2025/2/19
 */
abstract class BaseAdminDataLists extends BaseDataLists
{
    protected array $adminInfo;
    protected int $adminId;

    public function __construct()
    {
        parent::__construct();
        $this->adminInfo = $this->request->adminInfo;
        $this->adminId = $this->request->adminId;
    }

}