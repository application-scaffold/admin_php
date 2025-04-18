<?php

namespace app\common\logic;

use app\common\enum\user\AccountLogEnum;
use app\common\model\user\UserAccountLog;
use app\common\model\user\User;


/**
 * 账号操作审计
 * @class AccountLogLogic
 * @package app\common\logic
 * @author LZH
 * @date 2025/2/18
 */
class AccountLogLogic extends BaseLogic
{

    /**
     * 记录账号操作
     * @param $userId
     * @param $changeType
     * @param $action
     * @param $changeAmount
     * @param string $sourceSn
     * @param string $remark
     * @param array $extra
     * @return UserAccountLog|false|\think\Model
     * @author LZH
     * @date 2025/2/18
     */
    public static function add($userId, $changeType, $action, $changeAmount, string $sourceSn = '', string $remark = '',  array $extra = [])
    {
        $user = User::findOrEmpty($userId);
        if($user->isEmpty()) {
            return false;
        }

        $changeObject = AccountLogEnum::getChangeObject($changeType);
        if(!$changeObject) {
            return false;
        }

        switch ($changeObject) {
            // 用户余额
            case AccountLogEnum::UM:
                $left_amount = $user->user_money;
                break;
            // 其他
        }

        $data = [
            'sn' => generate_sn(UserAccountLog::class, 'sn', 20),
            'user_id' => $userId,
            'change_object' => $changeObject,
            'change_type' => $changeType,
            'action' => $action,
            'left_amount' => $left_amount,
            'change_amount' => $changeAmount,
            'source_sn' => $sourceSn,
            'remark' => $remark,
            'extra' => $extra ? json_encode($extra, JSON_UNESCAPED_UNICODE) : '',
        ];
        return UserAccountLog::create($data);
    }
}