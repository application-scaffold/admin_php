<?php
declare(strict_types=1);

namespace app\admin_api\logic\decorate;

use app\common\logic\BaseLogic;
use app\common\model\decorate\DecoratePage;


/**
 * 装修页面
 * @class DecoratePageLogic
 * @package app\admin_api\logic\decorate
 * @author LZH
 * @date 2025/2/19
 */
class DecoratePageLogic extends BaseLogic
{

    /**
     * 获取详情
     * @param int $id
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getDetail(int $id): array
    {
        return DecoratePage::findOrEmpty($id)->toArray();
    }

    /**
     * 保存装修配置
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function save(array $params): bool
    {
        $pageData = DecoratePage::where(['id' => $params['id']])->findOrEmpty();
        if ($pageData->isEmpty()) {
            self::$error = '信息不存在';
            return false;
        }
        DecoratePage::update([
            'id' => $params['id'],
            'type' => $params['type'],
            'data' => $params['data'],
            'meta' => $params['meta'] ?? '',
        ]);
        return true;
    }

}