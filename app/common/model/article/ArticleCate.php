<?php
declare (strict_types = 1);

namespace app\common\model\article;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use think\model\relation\HasMany;

/**
 * 资讯分类管理模型
 * @class ArticleCate
 * @package app\common\model\article
 * @author LZH
 * @date 2025/2/18
 */
class ArticleCate extends BaseModel
{
    use SoftDelete;

    protected string $deleteTime = 'delete_time';


    /**
     * 关联文章
     * @return HasMany
     * @author LZH
     * @date 2025/2/18
     */
    public function article(): HasMany
    {
        return $this->hasMany(Article::class, 'cid', 'id');
    }


    /**
     * 状态描述
     * @param mixed $value
     * @param array $data
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getIsShowDescAttr(mixed $value, array $data): string
    {
        return $data['is_show'] ? '启用' : '停用';
    }

    /**
     * 文章数量
     * @param mixed $value
     * @param array $data
     * @return int
     * @author LZH
     * @date 2025/2/18
     */
    public function getArticleCountAttr(mixed $value, array $data): int
    {
        return Article::where(['cid' => $data['id']])->count('id');
    }

}