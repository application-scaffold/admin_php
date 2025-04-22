<?php
declare(strict_types=1);

namespace app\admin_api\logic\dept;

use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\dept\Dept;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;


/**
 * 部门管理逻辑
 * @class DeptLogic
 * @package app\admin_api\logic\dept
 * @author LZH
 * @date 2025/2/19
 */
class DeptLogic extends BaseLogic
{

    /**
     * 部门列表
     * @param array $params
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function lists(array $params): array
    {
        $where = [];
        if (!empty($params['name'])) {
            $where[] = ['name', 'like', '%' . $params['name'] . '%'];
        }
        if (isset($params['status']) && $params['status'] != '') {
            $where[] = ['status', '=', $params['status']];
        }
        $lists = Dept::where($where)
            ->append(['status_desc'])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();

        $pid = 0;
        if (!empty($lists)) {
            $pid = intval(min(array_column($lists, 'pid')));
        }
        return self::getTree($lists, $pid);
    }

    /**
     * 列表树状结构
     * @param array $array
     * @param int $pid
     * @param int $level
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getTree(array $array, int $pid = 0, int $level = 0): array
    {
        $list = [];
        foreach ($array as $key => $item) {
            if ($item['pid'] == $pid) {
                $item['level'] = $level;
                $item['children'] = self::getTree($array, $item['id'], $level + 1);
                $list[] = $item;
            }
        }
        return $list;
    }

    /**
     * 上级部门
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function leaderDept(): array
    {
        $lists = Dept::field(['id', 'name'])->where(['status' => 1])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();
        return $lists;
    }


    /**
     * 添加部门
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function add(array $params): void
    {
        Dept::create([
            'pid' => $params['pid'],
            'name' => $params['name'],
            'leader' => $params['leader'] ?? '',
            'mobile' => $params['mobile'] ?? '',
            'status' => $params['status'],
            'sort' => $params['sort'] ?? 0
        ]);
    }


    /**
     * 编辑部门
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function edit(array $params): bool
    {
        try {
            $pid = $params['pid'];
            $oldDeptData = Dept::findOrEmpty($params['id']);
            if ($oldDeptData['pid'] == 0) {
                $pid = 0;
            }

            Dept::update([
                'id' => $params['id'],
                'pid' => $pid,
                'name' => $params['name'],
                'leader' => $params['leader'] ?? '',
                'mobile' => $params['mobile'] ?? '',
                'status' => $params['status'],
                'sort' => $params['sort'] ?? 0
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * 删除部门
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function delete(array $params): void
    {
        Dept::destroy($params['id']);
    }


    /**
     * 获取部门详情
     * @param $params
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function detail($params): array
    {
        return Dept::findOrEmpty($params['id'])->toArray();
    }


    /**
     * 部门数据
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function getAllData(): array
    {
        $data = Dept::where(['status' => YesNoEnum::YES])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();

        $pid = min(array_column($data, 'pid'));
        return self::getTree($data, $pid);
    }

}