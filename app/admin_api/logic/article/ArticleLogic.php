<?php

namespace app\admin_api\logic\article;

use app\common\logic\BaseLogic;
use app\common\model\article\Article;
use app\common\service\FileService;

/**
 * 资讯管理逻辑
 * @class ArticleLogic
 * @package app\admin_api\logic\article
 * @author LZH
 * @date 2025/2/19
 */
class ArticleLogic extends BaseLogic
{

    /**
     * 添加资讯
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function add(array $params)
    {
        Article::create([
            'title' => $params['title'],
            'desc' => $params['desc'] ?? '',
            'author' => $params['author'] ?? '', //作者
            'sort' => $params['sort'] ?? 0, // 排序
            'abstract' => $params['abstract'], // 文章摘要
            'click_virtual' => $params['click_virtual'] ?? 0,
            'image' => $params['image'] ? FileService::setFileUrl($params['image']) : '',
            'cid' => $params['cid'],
            'is_show' => $params['is_show'],
            'content' => $params['content'] ?? '',
        ]);
    }

    /**
     * 编辑资讯
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function edit(array $params) : bool
    {
        try {
            Article::update([
                'id' => $params['id'],
                'title' => $params['title'],
                'desc' => $params['desc'] ?? '', // 简介
                'author' => $params['author'] ?? '', //作者
                'sort' => $params['sort'] ?? 0, // 排序
                'abstract' => $params['abstract'], // 文章摘要
                'click_virtual' => $params['click_virtual'] ?? 0,
                'image' => $params['image'] ? FileService::setFileUrl($params['image']) : '',
                'cid' => $params['cid'],
                'is_show' => $params['is_show'],
                'content' => $params['content'] ?? '',
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * 删除资讯
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function delete(array $params)
    {
        Article::destroy($params['id']);
    }

    /**
     * 查看资讯详情
     * @param $params
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function detail($params) : array
    {
        return Article::findOrEmpty($params['id'])->toArray();
    }

    /**
     * 更改资讯状态
     * @param array $params
     * @return true
     * @author LZH
     * @date 2025/2/19
     */
    public static function updateStatus(array $params)
    {
        Article::update([
            'id' => $params['id'],
            'is_show' => $params['is_show']
        ]);
        return true;
    }
}