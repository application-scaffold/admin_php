<?php
declare(strict_types=1);

namespace app\admin_api\validate\auth;

use app\common\validate\BaseValidate;
use app\common\model\auth\{SystemRole,SystemMenu};

/**
 * 系统菜单
 * @class MenuValidate
 * @package app\admin_api\validate\auth
 * @author LZH
 * @date 2025/2/19
 */
class MenuValidate extends BaseValidate
{

    protected $rule = [
        'id' => 'require',
        'pid' => 'require|checkPid',
        'type' => 'require|in:M,C,A',
        'name' => 'require|length:1,30|checkUniqueName',
        'icon' => 'max:100',
        'sort' => 'require|egt:0',
        'perms' => 'max:100',
        'paths' => 'max:200',
        'component' => 'max:200',
        'selected' => 'max:200',
        'params' => 'max:200',
        'is_cache' => 'require|in:0,1',
        'is_show' => 'require|in:0,1',
        'is_disable' => 'require|in:0,1',
    ];


    protected $message = [
        'id.require' => '参数缺失',
        'pid.require' => '请选择上级菜单',
        'type.require' => '请选择菜单类型',
        'type.in' => '菜单类型参数值错误',
        'name.require' => '请填写菜单名称',
        'name.length' => '菜单名称长度需为1~30个字符',
        'icon.max' => '图标名称不能超过100个字符',
        'sort.require' => '请填写排序',
        'sort.egt' => '排序值需大于或等于0',
        'perms.max' => '权限字符不能超过100个字符',
        'paths.max' => '路由地址不能超过200个字符',
        'component.max' => '组件路径不能超过200个字符',
        'selected.max' => '选中菜单路径不能超过200个字符',
        'params.max' => '路由参数不能超过200个字符',
        'is_cache.require' => '请选择缓存状态',
        'is_cache.in' => '缓存状态参数值错误',
        'is_show.require' => '请选择显示状态',
        'is_show.in' => '显示状态参数值错误',
        'is_disable.require' => '请选择菜单状态',
        'is_disable.in' => '菜单状态参数值错误',
    ];


    /**
     * 添加场景
     * @return MenuValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneAdd(): MenuValidate
    {
        return $this->remove('id', true);
    }


    /**
     * 详情场景
     * @return MenuValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDetail(): MenuValidate
    {
        return $this->only(['id']);
    }

    /**
     * 删除场景
     * @return MenuValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDelete(): MenuValidate
    {
        return $this->only(['id'])
            ->append('id', 'checkAbleDelete');
    }

    /**
     * 更新状态场景
     * @return MenuValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneStatus(): MenuValidate
    {
        return $this->only(['id', 'is_disable']);
    }

    /**
     * 校验菜单名称是否已存在
     * @param bool $value
     * @param string $rule
     * @param array $data
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    protected function checkUniqueName(bool $value, string $rule, array $data): bool|string
    {
        if ($data['type'] != 'M') {
            return true;
        }
        $where[] = ['type', '=', $data['type']];
        $where[] = ['name', '=', $data['name']];

        if (!empty($data['id'])) {
            $where[] = ['id', '<>', $data['id']];
        }

        $check = SystemMenu::where($where)->findOrEmpty();

        if (!$check->isEmpty()) {
            return '菜单名称已存在';
        }

        return true;
    }


    /**
     * 是否有子级菜单
     * @param string $value
     * @param string $rule
     * @param array $data
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    protected function checkAbleDelete(string $value, string $rule, array $data): bool|string
    {
        $hasChild = SystemMenu::where(['pid' => $value])->findOrEmpty();
        if (!$hasChild->isEmpty()) {
            return '存在子菜单,不允许删除';
        }

        // 已绑定角色菜单不可以删除
        $isBindRole = SystemRole::hasWhere('roleMenuIndex', ['menu_id' => $value])->findOrEmpty();
        if (!$isBindRole->isEmpty()) {
            return '已分配菜单不可删除';
        }

        return true;
    }

    /**
     * 校验上级
     * @param string $value
     * @param string $rule
     * @param array $data
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    protected function checkPid(string $value, string $rule, array $data): bool|string
    {
        if (!empty($data['id']) && $data['id'] == $value) {
            return '上级菜单不能选择自己';
        }
        return true;
    }

}