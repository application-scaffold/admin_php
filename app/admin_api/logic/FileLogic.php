<?php

namespace app\admin_api\logic;

use app\common\logic\BaseLogic;
use app\common\model\file\File;
use app\common\model\file\FileCate;
use app\common\service\ConfigService;
use app\common\service\storage\Driver as StorageDriver;

/**
 * 文件逻辑层
 * @class FileLogic
 * @package app\admin_api\logic
 * @author LZH
 * @date 2025/2/19
 */
class FileLogic extends BaseLogic
{

    /**
     * 移动文件
     * @param $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function move($params)
    {
        (new File())->whereIn('id', $params['ids'])
            ->update([
                'cid' => $params['cid'],
                'update_time' => time()
            ]);
    }

    /**
     * 重命名文件
     * @param $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function rename($params)
    {
        (new File())->where('id', $params['id'])
            ->update([
                'name' => $params['name'],
                'update_time' => time()
            ]);
    }

    /**
     * 批量删除文件
     * @param $params
     * @return void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function delete($params)
    {
        $result = File::whereIn('id', $params['ids'])->select();
        $StorageDriver = new StorageDriver([
            'default' => ConfigService::get('storage', 'default', 'local'),
            'engine'  => ConfigService::get('storage') ?? ['local'=>[]],
        ]);
        foreach ($result as $item) {
            $StorageDriver->delete($item['uri']);
        }
        File::destroy($params['ids']);
    }

    /**
     * 添加文件分类
     * @param $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function addCate($params)
    {
        FileCate::create([
            'type' => $params['type'],
            'pid' => $params['pid'],
            'name' => $params['name']
        ]);
    }


    /**
     * 编辑文件分类
     * @param $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function editCate($params)
    {
        FileCate::update([
            'name' => $params['name'],
            'update_time' => time()
        ], ['id' => $params['id']]);
    }

    /**
     * 删除文件分类
     * @param $params
     * @return void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function delCate($params)
    {
        $fileModel = new File();
        $cateModel = new FileCate();

        $cateIds = self::getCateIds($params['id']);
        array_push($cateIds, $params['id']);

        // 删除分类及子分类
        $cateModel->whereIn('id', $cateIds)->update(['delete_time' => time()]);

        // 删除文件
        $fileIds = $fileModel->whereIn('cid', $cateIds)->column('id');

        if (!empty($fileIds)) {
            self::delete(['ids' => $fileIds]);
        }
    }

    /**
     * 获取所有分类id
     * @param $parentId
     * @param array $cateArr
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getCateIds($parentId, array $cateArr = []): array
    {
        $childIds = FileCate::where(['pid' => $parentId])->column('id');

        if (empty($childIds)) {
            return $childIds;
        } else {
            $allChildIds = $childIds;
            foreach ($childIds as $childId) {
                $allChildIds = array_merge($allChildIds, static::getCateIds($childId, $cateArr));
            }
            return $allChildIds;
        }
    }

}