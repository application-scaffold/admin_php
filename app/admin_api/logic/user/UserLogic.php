<?php
declare(strict_types=1);

namespace app\admin_api\logic\user;

use app\common\enum\user\AccountLogEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\logic\AccountLogLogic;
use app\common\logic\BaseLogic;
use app\common\model\user\User;
use think\facade\Db;
use think\model\contract\Modelable;

/**
 * 用户逻辑层
 * @class UserLogic
 * @package app\admin_api\logic\user
 * @author LZH
 * @date 2025/2/19
 */
class UserLogic extends BaseLogic
{

    /**
     * 用户详情
     * @param int $userId
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function detail(int $userId): array
    {
        $field = [
            'id', 'sn', 'account', 'nickname', 'avatar', 'real_name',
            'sex', 'mobile', 'create_time', 'login_time', 'channel',
            'user_money',
        ];

        $user = User::where(['id' => $userId])->field($field)
            ->findOrEmpty();

        $user['channel'] = UserTerminalEnum::getTermInalDesc($user['channel']);
        $user->sex = $user->getData('sex');
        return $user->toArray();
    }


    /**
     * 更新用户信息
     * @param array $params
     * @return User|Modelable
     * @author LZH
     * @date 2025/2/19
     */
    public static function setUserInfo(array $params): User|Modelable
    {
        return User::update([
            'id' => $params['id'],
            $params['field'] => $params['value']
        ]);
    }

    /**
     * 调整用户余额
     * @param array $params
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    public static function adjustUserMoney(array $params): bool|string
    {
        Db::startTrans();
        try {
            $user = User::find($params['user_id']);
            if (AccountLogEnum::INC == $params['action']) {
                //调整可用余额
                $user->user_money += $params['num'];
                $user->save();
                //记录日志
                AccountLogLogic::add(
                    $user->id,
                    AccountLogEnum::UM_INC_ADMIN,
                    AccountLogEnum::INC,
                    $params['num'],
                    '',
                    $params['remark'] ?? ''
                );
            } else {
                $user->user_money -= $params['num'];
                $user->save();
                //记录日志
                AccountLogLogic::add(
                    $user->id,
                    AccountLogEnum::UM_DEC_ADMIN,
                    AccountLogEnum::DEC,
                    $params['num'],
                    '',
                    $params['remark'] ?? ''
                );
            }

            Db::commit();
            return true;

        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

}