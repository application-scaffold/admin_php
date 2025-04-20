<?php

namespace app\admin_api\validate;

use app\common\validate\BaseValidate;

/**
 * 文件验证
 * @class FileValidate
 * @package app\admin_api\validate
 * @author LZH
 * @date 2025/2/19
 */
class FileValidate extends BaseValidate
{
    protected $rule = [
        'id'   => 'require|number',
        'cid'  => 'require|number',
        'ids'  => 'require|array',
        'type' => 'require|in:10,20,30',
        'pid'  => 'require|number',
        'name' => 'require|max:20'
    ];

    protected $message = [
        'id.require'   => '缺少id参数',
        'cid.require'  => '缺少cid参数',
        'ids.require'  => '缺少ids参数',
        'type.require' => '缺少type参数',
        'pid.require'  => '缺少pid参数',
        'name.require' => '请填写分组名称',
        'name.max' => '分组名称长度须为20字符内',
    ];


    /**
     * id验证场景
     * @return FileValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneId()
    {
        return $this->only(['id']);
    }


    /**
     * 重命名文件场景
     * @return FileValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneRename()
    {
        return $this->only(['id', 'name']);
    }


    /**
     * 新增分类场景
     * @return FileValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneAddCate()
    {
        return $this->only(['type', 'pid', 'name']);
    }

    /**
     * 编辑分类场景
     * @return FileValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneEditCate()
    {
        return $this->only(['id', 'name']);
    }


    /**
     * 移动场景
     * @return FileValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneMove()
    {
        return $this->only(['ids', 'cid']);
    }


    /**
     * 删除场景
     * @return FileValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDelete()
    {
        return $this->only(['ids']);
    }
}