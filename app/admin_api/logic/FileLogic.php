<?php
declare(strict_types=1);

namespace app\admin_api\logic;

use app\common\logic\BaseLogic;
use app\common\model\file\File;
use app\common\model\file\FileCate;
use app\common\service\ConfigService;
use app\common\service\storage\Driver as StorageDriver;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Exception;

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
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function move(array $params): void
    {
        (new File())->whereIn('id', $params['ids'])
            ->update([
                'cid' => $params['cid'],
                'update_time' => time()
            ]);
    }

    /**
     * 重命名文件
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function rename(array $params): void
    {
        (new File())->where('id', $params['id'])
            ->update([
                'name' => $params['name'],
                'update_time' => time()
            ]);
    }

    /**
     * 批量删除文件
     * @param array $params
     * @return void
     * @throws Exception
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function delete(array $params): void
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
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function addCate(array $params): void
    {
        FileCate::create([
            'type' => $params['type'],
            'pid' => $params['pid'],
            'name' => $params['name']
        ]);
    }


    /**
     * 编辑文件分类
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function editCate(array $params): void
    {
        FileCate::update([
            'name' => $params['name'],
            'update_time' => time()
        ], ['id' => $params['id']]);
    }

    /**
     * 删除文件分类
     * @param array $params
     * @return void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws Exception
     * @throws ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function delCate(array $params): void
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
     * @param string $parentId
     * @param array $cateArr
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getCateIds(string $parentId, array $cateArr = []): array
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