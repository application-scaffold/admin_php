<?php
declare(strict_types=1);

namespace app\admin_api\logic\setting;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;


/**
 * 交易设置逻辑
 * @class TransactionSettingsLogic
 * @package app\admin_api\logic\setting
 * @author LZH
 * @date 2025/2/19
 */
class TransactionSettingsLogic extends BaseLogic
{
    /**
     * 获取交易设置
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getConfig(): array
    {
        $config = [
            'cancel_unpaid_orders' => ConfigService::get('transaction', 'cancel_unpaid_orders', 1),
            'cancel_unpaid_orders_times' => ConfigService::get('transaction', 'cancel_unpaid_orders_times', 30),
            'verification_orders' => ConfigService::get('transaction', 'verification_orders', 1),
            'verification_orders_times' => ConfigService::get('transaction', 'verification_orders_times', 24),
        ];

        return $config;
    }

    /**
     * 设置交易设置
     * @param array $params
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public static function setConfig(array $params): void
    {
        ConfigService::set('transaction', 'cancel_unpaid_orders', $params['cancel_unpaid_orders']);
        ConfigService::set('transaction', 'verification_orders', $params['verification_orders']);

        if (isset($params['cancel_unpaid_orders_times'])) {
            ConfigService::set('transaction', 'cancel_unpaid_orders_times', $params['cancel_unpaid_orders_times']);
        }

        if (isset($params['verification_orders_times'])) {
            ConfigService::set('transaction', 'verification_orders_times', $params['verification_orders_times']);
        }
    }
}