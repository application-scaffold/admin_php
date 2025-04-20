<?php

namespace app\common\model\article;

use app\common\enum\YesNoEnum;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 资讯管理模型
 * @class Article
 * @package app\common\model\article
 * @author LZH
 * @date 2025/2/18
 */
class Article extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    /**
     * 获取分类名称
     * @param $value
     * @param $data
     * @return mixed
     * @author LZH
     * @date 2025/2/18
     */
    public function getCateNameAttr($value, $data)
    {
        return ArticleCate::where('id', $data['cid'])->value('name');
    }

    /**
     * 浏览量
     * @param $value
     * @param $data
     * @return mixed
     * @author LZH
     * @date 2025/2/18
     */
    public function getClickAttr($value, $data)
    {
        return $data['click_actual'] + $data['click_virtual'];
    }


    /**
     * 设置图片域名
     * @param $value
     * @param $data
     * @return mixed
     * @author LZH
     * @date 2025/2/18
     */
    public function getContentAttr($value, $data)
    {
        return get_file_domain($value);
    }


    /**
     * 清除图片域名
     * @param $value
     * @param $data
     * @return mixed
     * @author LZH
     * @date 2025/2/18
     */
    public function setContentAttr($value, $data)
    {
        return clear_file_domain($value);
    }


    /**
     * 获取文章详情
     * @param int $id
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public static function getArticleDetailArr(int $id)
    {
        $article = Article::where(['id' => $id, 'is_show' => YesNoEnum::YES])
            ->findOrEmpty();

        if ($article->isEmpty()) {
            return [];
        }

        // 增加点击量
        $article->click_actual += 1;
        $article->save();

        return $article->append(['click'])
            ->hidden(['click_virtual', 'click_actual'])
            ->toArray();
    }

}