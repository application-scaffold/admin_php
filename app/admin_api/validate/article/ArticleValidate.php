<?php
declare(strict_types=1);

namespace app\admin_api\validate\article;

use app\common\validate\BaseValidate;
use app\common\model\article\Article;

/**
 * 资讯管理验证
 * @class ArticleValidate
 * @package app\admin_api\validate\article
 * @author LZH
 * @date 2025/2/19
 */
class ArticleValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|checkArticle',
        'title' => 'require|length:1,255',
//        'image' => 'require',
        'cid' => 'require',
        'is_show' => 'require|in:0,1',
    ];

    protected $message = [
        'id.require' => '资讯id不能为空',
        'title.require' => '标题不能为空',
        'title.length' => '标题长度须在1-255位字符',
//        'image.require' => '封面图必须存在',
        'cid.require' => '所属栏目必须存在',
    ];

    /**
     * 添加场景
     * @return ArticleValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneAdd(): ArticleValidate
    {
        return $this->remove(['id'])
            ->remove('id','require|checkArticle');
    }

    /**
     * 详情场景
     * @return ArticleValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDetail(): ArticleValidate
    {
        return $this->only(['id']);
    }

    /**
     * 更改状态场景
     * @return ArticleValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneStatus(): ArticleValidate
    {
        return $this->only(['id', 'is_show']);
    }

    public function sceneEdit()
    {
    }

    /**
     * 删除场景
     * @return ArticleValidate
     * @author LZH
     * @date 2025/2/19
     */
    public function sceneDelete(): ArticleValidate
    {
        return $this->only(['id']);
    }

    /**
     * 检查指定资讯是否存在
     * @param string $value
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    public function checkArticle(string $value): bool|string
    {
        $article = Article::findOrEmpty($value);
        if ($article->isEmpty()) {
            return '资讯不存在';
        }
        return true;
    }

}