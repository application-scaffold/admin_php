<?php

namespace app\common\model\user;

use app\common\enum\user\UserEnum;
use app\common\model\BaseModel;
use app\common\service\FileService;
use think\model\concern\SoftDelete;

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

    protected $deleteTime = 'delete_time';

    /**
     * 关联用户授权模型
     * @return \think\model\relation\HasOne
     * @author LZH
     * @date 2025/2/18
     */
    public function userAuth()
    {
        return $this->hasOne(UserAuth::class, 'user_id');
    }

    /**
     * 搜索器-用户信息
     * @param $query
     * @param $value
     * @param $data
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function searchKeywordAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('sn|nickname|mobile|account', 'like', '%' . $value . '%');
        }
    }

    /**
     * 搜索器-注册来源
     * @param $query
     * @param $value
     * @param $data
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function searchChannelAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('channel', '=', $value);
        }
    }

    /**
     * 搜索器-注册时间
     * @param $query
     * @param $value
     * @param $data
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function searchCreateTimeStartAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('create_time', '>=', strtotime($value));
        }
    }

    /**
     * 搜索器-注册时间
     * @param $query
     * @param $value
     * @param $data
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function searchCreateTimeEndAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('create_time', '<=', strtotime($value));
        }
    }

    /**
     * 头像获取器 - 用于头像地址拼接域名
     * @param $value
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getAvatarAttr($value)
    {
        return trim($value) ? FileService::getFileUrl($value) : '';
    }


    /**
     * 获取器-性别描述
     * @param $value
     * @param $data
     * @return string|string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getSexAttr($value, $data)
    {
        return UserEnum::getSexDesc($value);
    }

    /**
     * 登录时间
     * @param $value
     * @return false|string
     * @author LZH
     * @date 2025/2/18
     */
    public function getLoginTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    /**
     * 生成用户编码
     * @param $prefix
     * @param $length
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/18
     */
    public static function createUserSn($prefix = '', $length = 8)
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