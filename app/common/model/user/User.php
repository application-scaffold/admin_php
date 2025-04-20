<?php
declare (strict_types = 1);

namespace app\common\model\user;

use app\common\enum\user\UserEnum;
use app\common\model\BaseModel;
use app\common\service\FileService;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\db\Query;
use think\model\concern\SoftDelete;
use think\model\relation\HasOne;

/**
 * 用户模型
 * @class User
 * @package app\common\model\user
 * @author LZH
 * @date 2025/2/18
 */
class User extends BaseModel
{
    use SoftDelete;

    protected string $deleteTime = 'delete_time';

    /**
     * 关联用户授权模型
     * @return HasOne
     * @author LZH
     * @date 2025/2/18
     */
    public function userAuth(): HasOne
    {
        return $this->hasOne(UserAuth::class, 'user_id');
    }

    /**
     * 搜索器-用户信息
     * @param Query $query
     * @param string $value
     * @param array $data
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function searchKeywordAttr(Query $query, string $value, array $data): void
    {
        if ($value) {
            $query->where('sn|nickname|mobile|account', 'like', '%' . $value . '%');
        }
    }

    /**
     * 搜索器-注册来源
     * @param Query $query
     * @param string $value
     * @param array $data
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function searchChannelAttr(Query $query, string $value, array $data): void
    {
        if ($value) {
            $query->where('channel', '=', $value);
        }
    }

    /**
     * 搜索器-注册时间
     * @param Query $query
     * @param string $value
     * @param array $data
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function searchCreateTimeStartAttr(Query $query, string $value, array $data): void
    {
        if ($value) {
            $query->where('create_time', '>=', strtotime($value));
        }
    }

    /**
     * 搜索器-注册时间
     * @param Query $query
     * @param string $value
     * @param array $data
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function searchCreateTimeEndAttr(Query $query, string $value, array $data): void
    {
        if ($value) {
            $query->where('create_time', '<=', strtotime($value));
        }
    }

    /**
     * 头像获取器 - 用于头像地址拼接域名
     * @param string $value
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getAvatarAttr(string $value): string
    {
        return trim($value) ? FileService::getFileUrl($value) : '';
    }


    /**
     * 获取器-性别描述
     * @param bool $value
     * @param array $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getSexAttr(bool $value, array $data): array|string
    {
        return UserEnum::getSexDesc($value);
    }

    /**
     * 登录时间
     * @param int $value
     * @return false|string
     * @author LZH
     * @date 2025/2/18
     */
    public function getLoginTimeAttr(int $value): bool|string
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    /**
     * 生成用户编码
     * @param string $prefix
     * @param int $length
     * @return string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author LZH
     * @date 2025/2/18
     */
    public static function createUserSn(string $prefix = '', int $length = 8): string
    {
        $rand_str = '';
        for ($i = 0; $i < $length; $i++) {
            $rand_str .= mt_rand(1, 9);
        }
        $sn = $prefix . $rand_str;
        if (User::where(['sn' => $sn])->find()) {
            return self::createUserSn($prefix, $length);
        }
        return $sn;
    }

}