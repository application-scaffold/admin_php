<?php
declare(strict_types=1);

namespace app\admin_api\logic\decorate;

use app\common\logic\BaseLogic;
use app\common\model\article\Article;
use app\common\model\decorate\DecoratePage;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;


/**
 * 装修页-数据
 * @class DecorateDataLogic
 * @package app\admin_api\logic\decorate
 * @author LZH
 * @date 2025/2/19
 */
class DecorateDataLogic extends BaseLogic
{

    /**
     * 获取文章列表
     * @param int $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public static function getArticleLists(int $limit): array
    {
        $field = 'id,title,desc,abstract,image,author,content,
        click_virtual,click_actual,create_time';

        return Article::where(['is_show' => 1])
            ->field($field)
            ->order(['id' => 'desc'])
            ->limit($limit)
            ->append(['click'])
            ->hidden(['click_virtual', 'click_actual'])
            ->select()->toArray();
    }

    /**
     * pc设置
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function pc(): array
    {
        $pcPage = DecoratePage::findOrEmpty(4)->toArray();
        $updateTime = !empty($pcPage['update_time']) ? $pcPage['update_time'] : date('Y-m-d H:i:s');
        return [
            'update_time' => $updateTime,
            'pc_url' => request()->domain() . '/pc'
        ];
    }

}