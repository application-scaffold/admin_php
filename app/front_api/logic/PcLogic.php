<?php
declare(strict_types=1);

namespace app\front_api\logic;

use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\article\Article;
use app\common\model\article\ArticleCate;
use app\common\model\article\ArticleCollect;
use app\common\model\decorate\DecoratePage;
use app\common\service\ConfigService;
use app\common\service\FileService;

/**
 * index
 * @class PcLogic
 * @package app\front_api\logic
 * @author LZH
 * @date 2025/2/20
 */
class PcLogic extends BaseLogic
{

    /**
     * 首页数据
     * @return array
     * @author LZH
     * @date 2025/2/20
     */
    public static function getIndexData(): array
    {
        // 装修配置
        $decoratePage = DecoratePage::findOrEmpty(4);
        // 最新资讯
        $newArticle = self::getLimitArticle('new', 7);
        // 全部资讯
        $allArticle = self::getLimitArticle('all', 5);
        // 热门资讯
        $hotArticle = self::getLimitArticle('hot', 8);

        return [
            'page' => $decoratePage,
            'all' => $allArticle,
            'new' => $newArticle,
            'hot' => $hotArticle
        ];
    }

    /**
     * 获取文章
     * @param string $sortType
     * @param int $limit
     * @param int $cate
     * @param int $excludeId
     * @return mixed
     * @author LZH
     * @date 2025/2/20
     */
    public static function getLimitArticle(string $sortType, int $limit = 0, int $cate = 0, int $excludeId = 0): mixed
    {
        // 查询字段
        $field = [
            'id', 'cid', 'title', 'desc', 'abstract', 'image',
            'author', 'click_actual', 'click_virtual', 'create_time'
        ];

        // 排序条件
        $orderRaw = 'sort desc, id desc';
        if ($sortType == 'new') {
            $orderRaw = 'id desc';
        }
        if ($sortType == 'hot') {
            $orderRaw = 'click_actual + click_virtual desc, id desc';
        }

        // 查询条件
        $where[] = ['is_show', '=', YesNoEnum::YES];
        if (!empty($cate)) {
            $where[] = ['cid', '=', $cate];
        }
        if (!empty($excludeId)) {
            $where[] = ['id', '<>', $excludeId];
        }

        $article = Article::field($field)
            ->where($where)
            ->append(['click'])
            ->orderRaw($orderRaw)
            ->hidden(['click_actual', 'click_virtual']);

        if ($limit) {
            $article->limit($limit);
        }

        return $article->select()->toArray();
    }


    /**
     * 获取配置
     * @return array
     * @author LZH
     * @date 2025/2/20
     */
    public static function getConfigData(): array
    {
        // 登录配置
        $loginConfig = [
            // 登录方式
            'login_way' => ConfigService::get('login', 'login_way', config('project.login.login_way')),
            // 注册强制绑定手机
            'coerce_mobile' => ConfigService::get('login', 'coerce_mobile', config('project.login.coerce_mobile')),
            // 政策协议
            'login_agreement' => ConfigService::get('login', 'login_agreement', config('project.login.login_agreement')),
            // 第三方登录 开关
            'third_auth' => ConfigService::get('login', 'third_auth', config('project.login.third_auth')),
            // 微信授权登录
            'wechat_auth' => ConfigService::get('login', 'wechat_auth', config('project.login.wechat_auth')),
            // qq授权登录
            'qq_auth' => ConfigService::get('login', 'qq_auth', config('project.login.qq_auth')),
        ];

        // 网站信息
        $website = [
            'shop_name' => ConfigService::get('website', 'shop_name'),
            'shop_logo' => FileService::getFileUrl(ConfigService::get('website', 'shop_logo')),
            'pc_logo' => FileService::getFileUrl(ConfigService::get('website', 'pc_logo')),
            'pc_title' => ConfigService::get('website', 'pc_title'),
            'pc_ico' => FileService::getFileUrl(ConfigService::get('website', 'pc_ico')),
            'pc_desc' => ConfigService::get('website', 'pc_desc'),
            'pc_keywords' => ConfigService::get('website', 'pc_keywords'),
        ];

        // 站点统计
        $siteStatistics = [
            'clarity_code' => ConfigService::get('siteStatistics', 'clarity_code'),
        ];

        // 备案信息
        $copyright = ConfigService::get('copyright', 'config', []);

        // 公众号二维码
        $oaQrCode = ConfigService::get('oa_setting', 'qr_code', '');
        $oaQrCode = empty($oaQrCode) ? $oaQrCode : FileService::getFileUrl($oaQrCode);
        // 小程序二维码
        $mnpQrCode = ConfigService::get('mnp_setting', 'qr_code', '');
        $mnpQrCode = empty($mnpQrCode) ? $mnpQrCode : FileService::getFileUrl($mnpQrCode);

        return [
            'domain' => FileService::getFileUrl(),
            'login' => $loginConfig,
            'website' => $website,
            'siteStatistics' => $siteStatistics,
            'version' => config('project.version'),
            'copyright' => $copyright,
            'admin_url' => request()->domain() . '/admin',
            'qrcode' => [
                'oa' => $oaQrCode,
                'mnp' => $mnpQrCode,
            ]
        ];
    }

    /**
     * 资讯中心
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/20
     */
    public static function getInfoCenter(): array
    {
        $data = ArticleCate::field(['id', 'name'])
            ->with(['article' => function ($query) {
                $query->hidden(['content', 'click_virtual', 'click_actual'])
                    ->order(['sort' => 'desc', 'id' => 'desc'])
                    ->append(['click'])
                    ->limit(10);
            }])
            ->where(['is_show' => YesNoEnum::YES])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();

        return $data;
    }


    /**
     * 获取文章详情
     * @param int $userId
     * @param int $articleId
     * @param string $source
     * @return array
     * @author LZH
     * @date 2025/2/20
     */
    public static function getArticleDetail(int $userId, int $articleId, string $source = 'default')
    {
        // 文章详情
        $detail = Article::getArticleDetailArr($articleId);

        // 根据来源列表查找对应列表
        $nowIndex = 0;
        $lists = self::getLimitArticle($source, 0, $detail['cid']);
        foreach ($lists as $key => $item) {
            if ($item['id'] == $articleId) {
                $nowIndex = $key;
            }
        }
        // 上一篇
        $detail['last'] = $lists[$nowIndex - 1] ?? [];
        // 下一篇
        $detail['next'] = $lists[$nowIndex + 1] ?? [];

        // 最新资讯
        $detail['new'] = self::getLimitArticle('new', 8, $detail['cid'], $detail['id']);
        // 关注状态
        $detail['collect'] = ArticleCollect::isCollectArticle($userId, $articleId);
        // 分类名
        $detail['cate_name'] = ArticleCate::where('id', $detail['cid'])->value('name');

        return $detail;
    }

}