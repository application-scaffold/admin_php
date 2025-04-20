<?php

namespace app\admin_api\controller\tools;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\tools\DataTableLists;
use app\admin_api\lists\tools\GenerateTableLists;
use app\admin_api\logic\tools\GeneratorLogic;
use app\admin_api\validate\tools\EditTableValidate;
use app\admin_api\validate\tools\GenerateTableValidate;

/**
 * 代码生成器控制器
 * @class GeneratorController
 * @package app\admin_api\controller\tools
 * @author LZH
 * @date 2025/2/20
 */
class GeneratorController extends BaseAdminApiController
{

    public array $notNeedLogin = ['download'];

    /**
     * 获取数据库中所有数据表信息
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function dataTable()
    {
        return $this->dataLists(new DataTableLists());
    }

    /**
     * 获取已选择的数据表
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function generateTable()
    {
        return $this->dataLists(new GenerateTableLists());
    }


    /**
     * 选择数据表
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function selectTable()
    {
        $params = (new GenerateTableValidate())->post()->goCheck('select');
        $result = GeneratorLogic::selectTable($params, $this->adminId);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(GeneratorLogic::getError());
    }


    /**
     * 生成代码
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function generate()
    {
        $params = (new GenerateTableValidate())->post()->goCheck('id');
        $result = GeneratorLogic::generate($params);
        if (false === $result) {
            return $this->fail(GeneratorLogic::getError());
        }
        return $this->success('操作成功', $result, 1, 1);
    }

    /**
     * 下载文件
     * @return \think\response\File|\think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function download()
    {
        $params = (new GenerateTableValidate())->goCheck('download');
        $result = GeneratorLogic::download($params['file']);
        if (false === $result) {
            return $this->fail(GeneratorLogic::getError() ?: '下载失败');
        }
        return download($result, 'likeadmin-curd.zip');
    }

    /**
     * 预览代码
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function preview()
    {
        $params = (new GenerateTableValidate())->post()->goCheck('id');
        $result = GeneratorLogic::preview($params);
        if (false === $result) {
            return $this->fail(GeneratorLogic::getError());
        }
        return $this->data($result);
    }

    /**
     * 同步字段
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function syncColumn()
    {
        $params = (new GenerateTableValidate())->post()->goCheck('id');
        $result = GeneratorLogic::syncColumn($params);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(GeneratorLogic::getError());
    }


    /**
     * 编辑表信息
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function edit()
    {
        $params = (new EditTableValidate())->post()->goCheck();
        $result = GeneratorLogic::editTable($params);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(GeneratorLogic::getError());
    }

    /**
     * 获取已选择的数据表详情
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail()
    {
        $params = (new GenerateTableValidate())->goCheck('id');
        $result = GeneratorLogic::getTableDetail($params);
        return $this->success('', $result);
    }


    /**
     * 删除已选择的数据表信息
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function delete()
    {
        $params = (new GenerateTableValidate())->post()->goCheck('id');
        $result = GeneratorLogic::deleteTable($params);
        if (true === $result) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(GeneratorLogic::getError());
    }

    /**
     * 获取模型
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function getModels()
    {
        $result = GeneratorLogic::getAllModels();
        return $this->success('', $result, 1, 1);
    }

}

