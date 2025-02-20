<?php

namespace app\admin_api\controller\setting\dict;

use app\admin_api\controller\BaseAdminApiController;
use app\admin_api\lists\setting\dict\DictTypeLists;
use app\admin_api\logic\setting\dict\DictTypeLogic;
use app\admin_api\validate\dict\DictTypeValidate;

/**
 * 字典类型
 * @class DictTypeController
 * @package app\admin_api\controller\setting\dict
 * @author LZH
 * @date 2025/2/20
 */
class DictTypeController extends BaseAdminApiController
{

    /**
     * 获取字典类型列表
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function lists()
    {
        return $this->dataLists(new DictTypeLists());
    }

    /**
     * 添加字典类型
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function add()
    {
        $params = (new DictTypeValidate())->post()->goCheck('add');
        DictTypeLogic::add($params);
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * 编辑字典类型
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function edit()
    {
        $params = (new DictTypeValidate())->post()->goCheck('edit');
        DictTypeLogic::edit($params);
        return $this->success('编辑成功', [], 1, 1);
    }

    /**
     * 删除字典类型
     * @return \think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function delete()
    {
        $params = (new DictTypeValidate())->post()->goCheck('delete');
        DictTypeLogic::delete($params);
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
        $params = (new DictTypeValidate())->goCheck('detail');
        $result = DictTypeLogic::detail($params);
        return $this->data($result);
    }

    /**
     * 获取字典类型数据
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public function all()
    {
        $result = DictTypeLogic::getAllData();
        return $this->data($result);
    }

}