<?php
declare(strict_types=1);

namespace app\admin_api\lists\setting\system;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsSearchInterface;
use app\common\model\OperationLog;


/**
 * 日志列表
 * @class LogLists
 * @package app\admin_api\lists\setting\system
 * @author LZH
 * @date 2025/2/19
 */
class LogLists extends BaseAdminDataLists implements ListsSearchInterface, ListsExcelInterface
{

    /**
     * 设置搜索条件
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function setSearch(): array
    {
        return [
            '%like%' => ['admin_name','url','ip','type'],
            'between_time' => 'create_time',
        ];
    }

    /**
     * 查看系统日志列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $lists = OperationLog::field('id,action,admin_name,admin_id,url,type,params,ip,create_time')
            ->where($this->searchWhere)
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id','desc')
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * 查看系统日志总数
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        return OperationLog::where($this->searchWhere)->count();
    }

    /**
     * 设置导出字段
     * @return string[]
     * @author LZH
     * @date 2025/2/19
     */
    public function setExcelFields(): array
    {
        return [
            // '数据库字段名(支持别名) => 'Excel表字段名'
            'id' => '记录ID',
            'action' => '操作',
            'admin_name' => '管理员',
            'admin_id' => '管理员ID',
            'url' => '访问链接',
            'type' => '访问方式',
            'params' => '访问参数',
            'ip' => '来源IP',
            'create_time' => '日志时间',
        ];
    }

    /**
     * 设置默认表名
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function setFileName(): string
    {
        return '系统日志';
    }
}