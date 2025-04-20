<?php
declare(strict_types=1);

namespace app\admin_api\logic\channel;

use app\common\enum\OfficialAccountEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\channel\OfficialAccountReply;
use app\common\service\wechat\WeChatConfigService;
use app\common\service\wechat\WeChatOaService;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;


/**
 * 微信公众号回复逻辑层
 * @class OfficialAccountReplyLogic
 * @package app\admin_api\logic\channel
 * @author LZH
 * @date 2025/2/19
 */
class OfficialAccountReplyLogic extends BaseLogic
{

    /**
     * 添加回复(关注/关键词/默认)
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function add(array $params): bool
    {
        try {
            // 关键字回复排序值须大于0
            if ($params['reply_type'] == OfficialAccountEnum::REPLY_TYPE_KEYWORD && $params['sort'] < 0) {
                throw new \Exception('排序值须大于或等于0');
            }
            if ($params['reply_type'] != OfficialAccountEnum::REPLY_TYPE_KEYWORD && $params['status']) {
                // 非关键词回复只能有一条记录处于启用状态，所以将该回复类型下的已有记录置为禁用状态
                OfficialAccountReply::where(['reply_type' => $params['reply_type']])->update(['status' => YesNoEnum::NO]);
            }
            OfficialAccountReply::create($params);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * 查看回复详情
     * @param array $params
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function detail(array $params): array
    {
        $field = 'id,name,keyword,reply_type,matching_type,content_type,content,status,sort';
        $field .= ',reply_type as reply_type_desc, matching_type as matching_type_desc, content_type as content_type_desc, status as status_desc';
        return OfficialAccountReply::field($field)->findOrEmpty($params['id'])->toArray();
    }


    /**
     * 编辑回复(关注/关键词/默认)
     * @param array $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function edit(array $params): bool
    {
        try {
            // 关键字回复排序值须大于0
            if ($params['reply_type'] == OfficialAccountEnum::REPLY_TYPE_KEYWORD && $params['sort'] < 0) {
                throw new \Exception('排序值须大于或等于0');
            }
            if ($params['reply_type'] != OfficialAccountEnum::REPLY_TYPE_KEYWORD && $params['status']) {
                // 非关键词回复只能有一条记录处于启用状态，所以将该回复类型下的已有记录置为禁用状态
                OfficialAccountReply::where(['reply_type' => $params['reply_type']])->update(['status' => YesNoEnum::NO]);
            }
            OfficialAccountReply::update($params);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * 删除回复(关注/关键词/默认)
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function delete(array $params): void
    {
        OfficialAccountReply::destroy($params['id']);
    }


    /**
     * 更新排序
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function sort(array $params): void
    {
        $params['sort'] = $params['new_sort'];
        OfficialAccountReply::update($params);
    }


    /**
     * 更新状态
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function status(array $params): void
    {
        $reply = OfficialAccountReply::findOrEmpty($params['id']);
        $reply->status = !$reply->status;
        $reply->save();
    }


    /**
     * 微信公众号回调
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws \Throwable
     * @author LZH
     * @date 2025/2/19
     */
    public static function index(): mixed
    {
        $server = (new WeChatOaService())->getServer();
        // 事件
        $server->addMessageListener(OfficialAccountEnum::MSG_TYPE_EVENT, function ($message, \Closure $next) {
            switch ($message['Event']) {
                case OfficialAccountEnum::EVENT_SUBSCRIBE: // 关注事件
                    $replyContent = OfficialAccountReply::where([
                        'reply_type' => OfficialAccountEnum::REPLY_TYPE_FOLLOW,
                        'status' => YesNoEnum::YES
                    ])
                        ->value('content');

                    if ($replyContent) {
                        return $replyContent;
                    }
                    break;
            }
            return $next($message);
        });

        // 文本
        $server->addMessageListener(OfficialAccountEnum::MSG_TYPE_TEXT, function ($message, \Closure $next) {
            $replyList = OfficialAccountReply::where([
                'reply_type' => OfficialAccountEnum::REPLY_TYPE_KEYWORD,
                'status' => YesNoEnum::YES
            ])
                ->order('sort asc')
                ->select();

            $replyContent = '';
            foreach ($replyList as $reply) {
                switch ($reply['matching_type']) {
                    case OfficialAccountEnum::MATCHING_TYPE_FULL:
                        $reply['keyword'] === $message['Content'] && $replyContent = $reply['content'];
                        break;
                    case OfficialAccountEnum::MATCHING_TYPE_FUZZY:
                        stripos($message['Content'], $reply['keyword']) !== false && $replyContent = $reply['content'];
                        break;
                }
                if ($replyContent) {
                    break; // 得到回复文本，中止循环
                }
            }
            //消息回复为空的话，找默认回复
            if (empty($replyContent)) {
                $replyContent = static::getDefaultReply();
            }
            if ($replyContent) {
                return $replyContent;
            }
            return $next($message);
        });

        return $server->serve();
    }

    /**
     * 默认回复信息
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public static function getDefaultReply(): mixed
    {
        return OfficialAccountReply::where([
            'reply_type' => OfficialAccountEnum::REPLY_TYPE_DEFAULT,
            'status' => YesNoEnum::YES
        ])
            ->value('content');
    }
}