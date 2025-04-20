<?php

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

    protected $deleteTime = 'delete_time';

    protected $append = [
        'role_id',
        'dept_id',
        'jobs_id',
    ];

    /**
     * 关联角色id
     * @param $value
     * @param $data
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public function getRoleIdAttr($value, $data)
    {
        return AdminRole::where('admin_id', $data['id'])->column('role_id');
    }


    /**
     * 关联角色id
     * @param $value
     * @param $data
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public function getDeptIdAttr($value, $data)
    {
        return AdminDept::where('admin_id', $data['id'])->column('dept_id');
    }


    /**
     * 关联岗位id
     * @param $value
     * @param $data
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public function getJobsIdAttr($value, $data)
    {
        return AdminJobs::where('admin_id', $data['id'])->column('jobs_id');
    }

    /**
     * 获取禁用状态
     * @param $value
     * @param $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getDisableDescAttr($value, $data)
    {
        return YesNoEnum::getDisableDesc($data['disable']);
    }

    /**
     * 最后登录时间获取器 - 格式化：年-月-日 时:分:秒
     * @param $value
     * @return false|string
     * @author LZH
     * @date 2025/2/18
     */
    public function getLoginTimeAttr($value)
    {
        return empty($value) ? '' : date('Y-m-d H:i:s', $value);
    }

    /**
     * 头像获取器 - 头像路径添加域名
     * @param $value
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getAvatarAttr($value)
    {
        return empty($value) ? FileService::getFileUrl(config('project.default_image.admin_avatar')) : FileService::getFileUrl(trim($value, '/'));
    }

}