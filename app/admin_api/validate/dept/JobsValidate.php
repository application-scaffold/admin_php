<?php
declare(strict_types=1);

namespace app\admin_api\validate\dept;


use app\common\model\auth\Admin;
use app\common\model\auth\AdminJobs;
use app\common\model\dept\Jobs;
use app\common\validate\BaseValidate;


/**
 * 岗位验证
 * @class JobsValidate
 * @package app\admin_api\validate\dept
 * @author LZH
 * @date 2025/2/19
 */
class JobsValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkJobs',
        'name' => 'require|unique:'.Jobs::class.'|length:1,50',
        'code' => 'require|unique:'.Jobs::class,
        'status' => 'require|in:0,1',
        'sort' => 'egt:0',
    ];

    protected $message = [
        'id.require' => '参数缺失',
        'name.require' => '请填写岗位名称',
        'name.length' => '岗位名称长度须在1-50位字符',
        'name.unique' => '岗位名称已存在',
        'code.require' => '请填写岗位编码',
        'code.unique' => '岗位编码已存在',
        'sort.egt' => '排序值不正确',
        'status.require' => '请选择岗位状态',
        'status.in' => '岗位状态值错误',
    ];


    /**
     * 添加场景
     * @return JobsValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneAdd(): JobsValidate
    {
        return $this->remove('id', true);
    }

    /**
     * 详情场景
     * @return JobsValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDetail(): JobsValidate
    {
        return $this->only(['id']);
    }

    public function sceneEdit()
    {
    }


    /**
     * 删除场景
     * @return JobsValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDelete(): JobsValidate
    {
        return $this->only(['id'])->append('id', 'checkAbleDetele');
    }


    /**
     * 校验岗位
     * @param string $value
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    public function checkJobs(string $value): bool|string
    {
        $jobs = Jobs::findOrEmpty($value);
        if ($jobs->isEmpty()) {
            return '岗位不存在';
        }
        return true;
    }

    /**
     * 校验能否删除
     * @param string $value
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    public function checkAbleDetele(string $value): bool|string
    {
        $check = AdminJobs::where(['jobs_id' => $value])->findOrEmpty();
        if (!$check->isEmpty()) {
            return '已关联管理员，暂不可删除';
        }
        return true;
    }

}