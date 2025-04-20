<?php
declare(strict_types=1);

namespace app\admin_api\validate\auth;


use app\common\validate\BaseValidate;
use app\common\model\auth\{AdminRole, SystemRole, Admin};
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 角色验证器
 * @class RoleValidate
 * @package app\admin_api\validate\auth
 * @author LZH
 * @date 2025/2/19
 */
class RoleValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkRole',
        'name' => 'require|max:64|unique:' . SystemRole::class . ',name',
        'menu_id' => 'array',
    ];

    protected $message = [
        'id.require' => '请选择角色',
        'name.require' => '请输入角色名称',
        'name.max' => '角色名称最长为16个字符',
        'name.unique' => '角色名称已存在',
        'menu_id.array' => '权限格式错误'
    ];

    /**
     * 添加场景
     * @return RoleValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneAdd(): RoleValidate
    {
        return $this->only(['name', 'menu_id']);
    }

    /**
     * 详情场景
     * @return RoleValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDetail(): RoleValidate
    {
        return $this->only(['id']);
    }

    /**
     * 删除场景
     * @return RoleValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDel(): RoleValidate
    {
        return $this->only(['id'])
            ->append('id', 'checkAdmin');
    }


    /**
     * 验证角色是否存在
     * @param string $value
     * @param string $rule
     * @param array $data
     * @return string|true
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function checkRole(string $value, string $rule, array $data): bool|string
    {
        if (!SystemRole::find($value)) {
            return '角色不存在';
        }
        return true;
    }


    /**
     * 验证角色是否被使用
     * @param string $value
     * @param string $rule
     * @param array $data
     * @return string|true
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function checkAdmin(string $value, string $rule, array $data): bool|string
    {
        if (AdminRole::where(['role_id' => $value])->find()) {
            return '有管理员在使用该角色，不允许删除';
        }
        return true;
    }

}