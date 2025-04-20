<?php

namespace app\admin_api\controller;

use app\admin_api\lists\file\FileCateLists;
use app\admin_api\lists\file\FileLists;
use app\admin_api\logic\FileLogic;
use app\admin_api\validate\FileValidate;
use think\response\Json;

/**
 * 文件管理
 * @class FileController
 * @package app\admin_api\controller
 * @author LZH
 * @date 2025/2/20
 */
class FileController extends BaseAdminApiController
{

    /**
     * 文件列表
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists()
    {
        return $this->dataLists(new FileLists());
    }

    /**
     * 文件移动成功
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function move()
    {
        $params = (new FileValidate())->post()->goCheck('move');
        FileLogic::move($params);
        return $this->success('移动成功', [], 1, 1);
    }

    /**
     * 重命名文件
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function rename()
    {
        $params = (new FileValidate())->post()->goCheck('rename');
        FileLogic::rename($params);
        return $this->success('重命名成功', [], 1, 1);
    }

    /**
     * 删除文件
     * @return Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function delete()
    {
        $params = (new FileValidate())->post()->goCheck('delete');
        FileLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * 分类列表
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function listCate()
    {
        return $this->dataLists(new FileCateLists());
    }

    /**
     * 添加文件分类
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function addCate()
    {
        $params = (new FileValidate())->post()->goCheck('addCate');
        FileLogic::addCate($params);
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * 编辑文件分类
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function editCate()
    {
        $params = (new FileValidate())->post()->goCheck('editCate');
        FileLogic::editCate($params);
        return $this->success('编辑成功', [], 1, 1);
    }

    /**
     * 删除文件分类
     * @return Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function delCate()
    {
        $params = (new FileValidate())->post()->goCheck('id');
        FileLogic::delCate($params);
        return $this->success('删除成功', [], 1, 1);
    }
}