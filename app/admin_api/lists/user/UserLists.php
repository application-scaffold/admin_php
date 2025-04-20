<?php

namespace app\admin_api\lists\user;

use app\admin_api\lists\BaseAdminDataLists;
use app\common\enum\user\UserTerminalEnum;
use app\common\lists\ListsExcelInterface;
use app\common\model\user\User;


/**
 * 用户列表
 * @class UserLists
 * @package app\admin_api\lists\user
 * @author LZH
 * @date 2025/2/19
 */
class UserLists extends BaseAdminDataLists implements ListsExcelInterface
{

    /**
     * 搜索条件
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function setSearch(): array
    {
        $allowSearch = ['keyword', 'channel', 'create_time_start', 'create_time_end'];
        return array_intersect(array_keys($this->params), $allowSearch);
    }

    /**
     * 获取用户列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $field = "id,sn,nickname,sex,avatar,account,mobile,channel,create_time";
        $lists = User::withSearch($this->setSearch(), $this->params)
            ->limit($this->limitOffset, $this->limitLength)
            ->field($field)
            ->order('id desc')
            ->select()->toArray();

        foreach ($lists as &$item) {
            $item['channel'] = UserTerminalEnum::getTermInalDesc($item['channel']);
        }

        return $lists;
    }

    /**
     * 获取数量
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        return User::withSearch($this->setSearch(), $this->params)->count();
    }


    /**
     * 导出文件名
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function setFileName(): string
    {
        return '用户列表';
    }

    /**
     * 导出字段
     * @return string[]
     * @author LZH
     * @date 2025/2/19
     */
    public function setExcelFields(): array
    {
        return [
            'sn' => '用户编号',
            'nickname' => '用户昵称',
            'account' => '账号',
            'mobile' => '手机号码',
            'channel' => '注册来源',
            'create_time' => '注册时间',
        ];
    }

}