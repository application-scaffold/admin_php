<?php

declare (strict_types = 1);

namespace app\common\model\auth;

use app\common\enum\YesNoEnum;
use app\common\model\BaseModel;
use app\common\model\dept\Dept;
use think\model\concern\SoftDelete;
use app\common\service\FileService;

/**
 * @class Admin
 * @package app\common\model\auth
 * @author LZH
 * @date 2025/2/18
 */
class Admin extends BaseModel
{
    use SoftDelete;

    protected string $deleteTime = 'delete_time';

    protected array $append = [
        'role_id',
        'dept_id',
        'jobs_id',
    ];

    /**
     * 关联角色id
     * @param mixed $value
     * @param array $data
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public function getRoleIdAttr(mixed $value, array $data): array
    {
        return AdminRole::where('admin_id', $data['id'])->column('role_id');
    }


    /**
     * 关联角色id
     * @param mixed $value
     * @param array $data
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public function getDeptIdAttr(mixed $value, array $data): array
    {
        return AdminDept::where('admin_id', $data['id'])->column('dept_id');
    }


    /**
     * 关联岗位id
     * @param mixed $value
     * @param array $data
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public function getJobsIdAttr(mixed $value, array $data): array
    {
        return AdminJobs::where('admin_id', $data['id'])->column('jobs_id');
    }

    /**
     * 获取禁用状态
     * @param mixed $value
     * @param array $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getDisableDescAttr(mixed $value, array $data): array|string
    {
        return YesNoEnum::getDisableDesc($data['disable']);
    }

    /**
     * 最后登录时间获取器 - 格式化：年-月-日 时:分:秒
     * @param int $value
     * @param array $data
     * @return false|string
     * @author LZH
     * @date 2025/2/18
     */
    public function getLoginTimeAttr(int $value, array $data): bool|string
    {
        return empty($value) ? '' : date('Y-m-d H:i:s', $value);
    }

    /**
     * 头像获取器 - 头像路径添加域名
     * @param string $value
     * @param array $data
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getAvatarAttr(string $value, array $data): string
    {
        return empty($value) ? FileService::getFileUrl(config('project.default_image.admin_avatar')) : FileService::getFileUrl(trim($value, '/'));
    }

}