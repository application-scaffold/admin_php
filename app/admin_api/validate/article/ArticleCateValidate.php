<?php
declare(strict_types=1);

namespace app\admin_api\validate\article;

use app\common\validate\BaseValidate;
use app\common\model\article\ArticleCate;
use app\common\model\article\Article;

/**
 * 资讯分类管理验证
 * @class ArticleCateValidate
 * @package app\admin_api\validate\article
 * @author LZH
 * @date 2025/2/19
 */
class ArticleCateValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkArticleCate',
        'name' => 'require|length:1,90',
        'is_show' => 'require|in:0,1',
        'sort' => 'egt:0',
    ];

    protected $message = [
        'id.require' => '资讯分类id不能为空',
        'name.require' => '资讯分类不能为空',
        'name.length' => '资讯分类长度须在1-90位字符',
        'sort.egt' => '排序值不正确',
    ];

    /**
     * 添加场景
     * @return ArticleCateValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneAdd(): ArticleCateValidate
    {
        return $this->remove(['id'])
            ->remove('id', 'require|checkArticleCate');
    }

    /**
     * 详情场景
     * @return ArticleCateValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDetail(): ArticleCateValidate
    {
        return $this->only(['id']);
    }

    /**
     * 更改状态场景
     * @return ArticleCateValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneStatus(): ArticleCateValidate
    {
        return $this->only(['id', 'is_show']);
    }

    public function sceneEdit()
    {
    }

    /**
     * 获取所有资讯分类场景
     * @return ArticleCateValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneSelect(): ArticleCateValidate
    {
        return $this->only(['type']);
    }


    /**
     * 删除场景
     * @return ArticleCateValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDelete(): ArticleCateValidate
    {
        return $this->only(['id'])
            ->append('id', 'checkDeleteArticleCate');
    }

    /**
     * 检查指定资讯分类是否存在
     * @param string $value
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    public function checkArticleCate(string $value): bool|string
    {
        $article_category = ArticleCate::findOrEmpty($value);
        if ($article_category->isEmpty()) {
            return '资讯分类不存在';
        }
        return true;
    }

    /**
     * 删除时验证该资讯分类是否已使用
     * @param string $value
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    public function checkDeleteArticleCate(string $value): bool|string
    {
        $article = Article::where('cid', $value)->findOrEmpty();
        if (!$article->isEmpty()) {
            return '资讯分类已使用，请先删除绑定该资讯分类的资讯';
        }
        return true;
    }

}