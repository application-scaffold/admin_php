<?php
declare(strict_types=1);

namespace app\common\cache;

use app\common\model\auth\Admin;
use app\common\model\auth\AdminSession;
use app\common\model\auth\SystemRole;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 管理员token缓存
 * @class AdminTokenCache
 * @package app\common\cache
 * @author LZH
 * @date 2025/2/18
 */
class AdminTokenCache extends BaseCache
{
    // 缓存前缀
    private string $prefix = 'token_admin_';

    /**
     * 通过token获取缓存管理员信息
     * @param string $token 管理员token
     * @return array|false|mixed 管理员信息，如果不存在则返回false
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws \DateMalformedStringException
     * @author LZH
     * @date 2025/2/18
     */
    public function getAdminInfo(string $token): mixed
    {
        // 直接从缓存获取管理员信息
        $adminInfo = $this->get($this->prefix . $token);
        if ($adminInfo) {
            return $adminInfo;
        }

        // 如果缓存中没有，则从数据库获取并设置缓存
        $adminInfo = $this->setAdminInfo($token);
        if ($adminInfo) {
            return $adminInfo;
        }

        // 如果数据库中也找不到，返回false
        return false;
    }

    /**
     * 通过有效token设置管理员信息缓存
     * @param string $token 管理员token
     * @return array|false|mixed 管理员信息，如果token无效则返回空数组
     * @throws \DateMalformedStringException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/18
     */
    public function setAdminInfo(string $token): mixed
    {
        // 查询有效的管理员会话信息
        $adminSession = AdminSession::where([['token', '=', $token], ['expire_time', '>', time()]])
            ->find();
        if (empty($adminSession)) {
            return [];
        }

        // 查询管理员信息，并附加角色ID
        $admin = Admin::where('id', '=', $adminSession->admin_id)
            ->append(['role_id'])
            ->find();

        // 获取角色名称
        $roleName = '';
        $roleLists = SystemRole::column('name', 'id');
        if ($admin['root'] == 1) {
            // 如果是系统管理员，直接设置角色名称为“系统管理员”
            $roleName = '系统管理员';
        } else {
            // 否则，根据角色ID拼接角色名称
            foreach ($admin['role_id'] as $roleId) {
                $roleName .= $roleLists[$roleId] ?? '';
                $roleName .= '/';
            }
            $roleName = trim($roleName, '/');
        }

        // 构造管理员信息数组
        $adminInfo = [
            'admin_id' => $admin->id,
            'root' => $admin->root,
            'name' => $admin->name,
            'account' => $admin->account,
            'role_name' => $roleName,
            'role_id' => $admin->role_id,
            'token' => $token,
            'terminal' => $adminSession->terminal,
            'expire_time' => $adminSession->expire_time,
            'login_ip' => request()->ip(),
        ];

        // 将管理员信息保存到缓存，并设置过期时间
        $this->set($this->prefix . $token, $adminInfo, new \DateTime(Date('Y-m-d H:i:s', $adminSession->expire_time)));

        // 返回管理员信息
        return $this->getAdminInfo($token);
    }

    /**
     * 删除管理员信息缓存
     * @param string $token 管理员token
     * @return bool 删除成功返回true
     * @author LZH
     * @date 2025/2/18
     */
    public function deleteAdminInfo(string $token): bool
    {
        // 删除指定token的管理员信息缓存
        return $this->delete($this->prefix . $token);
    }
}
