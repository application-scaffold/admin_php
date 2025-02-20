<?php

namespace app\admin_api\controller\setting\dict;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\setting\dict\DictDataLists;
use app\admin_api\logic\setting\dict\DictDataLogic;
use app\admin_api\validate\dict\DictDataValidate;


/**
 * 字典数据
 * @class DictDataController
 * @package app\admin_api\controller\setting\dict
 * @author LZH
 * @date 2025/2/20
 */
class DictDataController extends BaseAdminApiController
{

    /**
     * 获取字典数据列表
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists()
    {
        return $this->dataLists(new DictDataLists());
    }

    /**
     * 添加字典数据
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function add()
    {
        $params = (new DictDataValidate())->post()->goCheck('add');
        DictDataLogic::save($params);
        return $this->success('添加成功', [], 1, 1);
    }


    /**
     * 编辑字典数据
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function edit()
    {
        $params = (new DictDataValidate())->post()->goCheck('edit');
        DictDataLogic::save($params);
        return $this->success('编辑成功', [], 1, 1);
    }


    /**
     * 删除字典数据
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function delete()
    {
        $params = (new DictDataValidate())->post()->goCheck('id');
        DictDataLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * 获取字典详情
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function detail()
    {
        $params = (new DictDataValidate())->goCheck('id');
        $result = DictDataLogic::detail($params);
        return $this->data($result);
    }

}