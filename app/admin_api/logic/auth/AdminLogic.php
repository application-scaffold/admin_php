<?php
declare(strict_types=1);

namespace app\admin_api\logic\auth;

use app\common\cache\AdminAuthCache;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\auth\Admin;
use app\common\model\auth\AdminDept;
use app\common\model\auth\AdminJobs;
use app\common\model\auth\AdminRole;
use app\common\model\auth\AdminSession;
use app\common\cache\AdminTokenCache;
use app\common\service\FileService;
use think\facade\Config;
use think\facade\Db;
use think\model\contract\Modelable;


/**
 * 管理员逻辑
 * @class AdminLogic
 * @package app\admin_api\logic\auth
 * @author LZH
 * @date 2025/2/19
 */
class AdminLogic extends BaseLogic
{

    /**
     * 添加管理员
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function add(array $params)
    {
        Db::startTrans();
        try {
            $passwordSalt = Config::get('project.unique_identification');
            $password = create_password($params['password'], $passwordSalt);
            $defaultAvatar = config('project.default_image.admin_avatar');
            $avatar = !empty($params['avatar']) ? FileService::setFileUrl($params['avatar']) : $defaultAvatar;

            $admin = Admin::create([
                'name' => $params['name'],
                'account' => $params['account'],
                'avatar' => $avatar,
                'password' => $password,
                'create_time' => time(),
                'disable' => $params['disable'],
                'multipoint_login' => $params['multipoint_login'],
            ]);

            // 角色
            self::insertRole($admin['id'], $params['role_id'] ?? []);
            // 部门
            self::insertDept($admin['id'], $params['dept_id'] ?? []);
            // 岗位
            self::insertJobs($admin['id'], $params['jobs_id'] ?? []);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 编辑管理员
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            // 基础信息
            $data = [
                'id' => $params['id'],
                'name' => $params['name'],
                'account' => $params['account'],
                'disable' => $params['disable'],
                'multipoint_login' => $params['multipoint_login']
            ];

            // 头像
            $data['avatar'] = !empty($params['avatar']) ? FileService::setFileUrl($params['avatar']) : '';

            // 密码
            if (!empty($params['password'])) {
                $passwordSalt = Config::get('project.unique_identification');
                $data['password'] = create_password($params['password'], $passwordSalt);
            }

            // 禁用或更换角色后.设置token过期
            $roleId = AdminRole::where('admin_id', $params['id'])->column('role_id');
            $editRole = false;
            if (!empty(array_diff_assoc($roleId, $params['role_id']))) {
                $editRole = true;
            }

            if ($params['disable'] == 1 || $editRole) {
                $tokenArr = AdminSession::where('admin_id', $params['id'])->select()->toArray();
                foreach ($tokenArr as $token) {
                    self::expireToken($token['token']);
                }
            }

            Admin::update($data);
            (new AdminAuthCache($params['id']))->clearAuthCache();

            // 删除旧的关联信息
            AdminRole::delByUserId($params['id']);
            AdminDept::delByUserId($params['id']);
            AdminJobs::delByUserId($params['id']);
            // 角色
            self::insertRole($params['id'], $params['role_id']);
            // 部门
            self::insertDept($params['id'], $params['dept_id'] ?? []);
            // 岗位
            self::insertJobs($params['id'], $params['jobs_id'] ?? []);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * 删除管理员
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function delete(array $params): bool
    {
        Db::startTrans();
        try {
            $admin = Admin::findOrEmpty($params['id']);
            if ($admin->root == YesNoEnum::YES) {
                throw new \Exception("超级管理员不允许被删除");
            }
            Admin::destroy($params['id']);

            //设置token过期
            $tokenArr = AdminSession::where('admin_id', $params['id'])->select()->toArray();
            foreach ($tokenArr as $token) {
                self::expireToken($token['token']);
            }
            (new AdminAuthCache($params['id']))->clearAuthCache();

            // 删除旧的关联信息
            AdminRole::delByUserId($params['id']);
            AdminDept::delByUserId($params['id']);
            AdminJobs::delByUserId($params['id']);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * 过期token
     * @param $token
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function expireToken(string $token): bool
    {
        $adminSession = AdminSession::where('token', '=', $token)
            ->with('admin')
            ->find();

        if (empty($adminSession)) {
            return false;
        }

        $time = time();
        $adminSession->expire_time = $time;
        $adminSession->update_time = $time;
        $adminSession->save();

        return (new AdminTokenCache())->deleteAdminInfo($token);
    }

    /**
     * 查看管理员详情
     * @param $params
     * @param string $action
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function detail(array $params, string $action = 'detail'): array
    {
        $admin = Admin::field([
            'id', 'account', 'name', 'disable', 'root',
            'multipoint_login', 'avatar',
        ])->findOrEmpty($params['id'])->toArray();

        if ($action == 'detail') {
            return $admin;
        }

        $result['user'] = $admin;
        // 当前管理员角色拥有的菜单
        $result['menu'] = MenuLogic::getMenuByAdminId($params['id']);
        // 当前管理员橘色拥有的按钮权限
        $result['permissions'] = AuthLogic::getBtnAuthByRoleId($admin);
        return $result;
    }

    /**
     * 编辑超级管理员
     * @param $params
     * @return Admin|Modelable
     * @author LZH
     * @date 2025/2/19
     */
    public static function editSelf(array $params): Modelable|Admin
    {
        $data = [
            'id' => $params['admin_id'],
            'name' => $params['name'],
            'avatar' => FileService::setFileUrl($params['avatar']),
        ];

        if (!empty($params['password'])) {
            $passwordSalt = Config::get('project.unique_identification');
            $data['password'] = create_password($params['password'], $passwordSalt);
        }

        return Admin::update($data);
    }


    /**
     * 新增角色
     * @param string $adminId
     * @param array $roleIds
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function insertRole(string $adminId, array $roleIds): void
    {
        if (!empty($roleIds)) {
            // 角色
            $roleData = [];
            foreach ($roleIds as $roleId) {
                $roleData[] = [
                    'admin_id' => $adminId,
                    'role_id' => $roleId,
                ];
            }
            (new AdminRole())->saveAll($roleData);
        }
    }


    /**
     * 新增部门
     * @param string $adminId
     * @param array $deptIds
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function insertDept(string $adminId, array $deptIds): void
    {
        // 部门
        if (!empty($deptIds)) {
            $deptData = [];
            foreach ($deptIds as $deptId) {
                $deptData[] = [
                    'admin_id' => $adminId,
                    'dept_id' => $deptId
                ];
            }
            (new AdminDept())->saveAll($deptData);
        }
    }


    /**
     * 新增岗位
     * @param string $adminId
     * @param array $jobsIds
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function insertJobs(string $adminId, array $jobsIds): void
    {
        // 岗位
        if (!empty($jobsIds)) {
            $jobsData = [];
            foreach ($jobsIds as $jobsId) {
                $jobsData[] = [
                    'admin_id' => $adminId,
                    'jobs_id' => $jobsId
                ];
            }
            (new AdminJobs())->saveAll($jobsData);
        }
    }

}