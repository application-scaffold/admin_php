<?php
declare(strict_types=1);

namespace app\admin_api\logic\article;

use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\article\ArticleCate;

/**
 * 资讯分类管理逻辑
 * @class ArticleCateLogic
 * @package app\admin_api\logic\article
 * @author LZH
 * @date 2025/2/19
 */
class ArticleCateLogic extends BaseLogic
{

    /**
     * 添加资讯分类
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function add(array $params): void
    {
        ArticleCate::create([
            'name' => $params['name'],
            'is_show' => $params['is_show'],
            'sort' => $params['sort'] ?? 0
        ]);
    }


    /**
     * 编辑资讯分类
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function edit(array $params) : bool
    {
        try {
            ArticleCate::update([
                'id' => $params['id'],
                'name' => $params['name'],
                'is_show' => $params['is_show'],
                'sort' => $params['sort'] ?? 0
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 删除资讯分类
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function delete(array $params): void
    {
        ArticleCate::destroy($params['id']);
    }

    /**
     * 查看资讯分类详情
     * @param $params
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function detail($params) : array
    {
        return ArticleCate::findOrEmpty($params['id'])->toArray();
    }

    /**
     * 更改资讯分类状态
     * @param array $params
     * @return true
     * @author LZH
     * @date 2025/2/19
     */
    public static function updateStatus(array $params): bool
    {
        ArticleCate::update([
            'id' => $params['id'],
            'is_show' => $params['is_show']
        ]);
        return true;
    }


    /**
     * 文章分类数据
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function getAllData(): array
    {
        return ArticleCate::where(['is_show' => YesNoEnum::YES])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();
    }

}