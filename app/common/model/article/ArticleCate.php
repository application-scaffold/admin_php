<?php

namespace app\common\model\article;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

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

    protected $deleteTime = 'delete_time';


    /**
     * 关联文章
     * @return \think\model\relation\HasMany
     * @author LZH
     * @date 2025/2/18
     */
    public function article()
    {
        return $this->hasMany(Article::class, 'cid', 'id');
    }


    /**
     * 状态描述
     * @param $value
     * @param $data
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getIsShowDescAttr($value, $data)
    {
        return $data['is_show'] ? '启用' : '停用';
    }

    /**
     * 文章数量
     * @param $value
     * @param $data
     * @return int
     * @throws \think\db\exception\DbException
     * @author LZH
     * @date 2025/2/18
     */
    public function getArticleCountAttr($value, $data)
    {
        return Article::where(['cid' => $data['id']])->count('id');
    }

}