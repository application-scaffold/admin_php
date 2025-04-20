<?php
declare(strict_types=1);

namespace app\admin_api\validate\setting;


use app\common\enum\PayEnum;
use app\common\model\pay\PayConfig;
use app\common\validate\BaseValidate;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;


class PayConfigValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require',
        'name' => 'require|checkName',
        'icon' => 'require',
        'sort' => 'require|number|max:5',
        'config' => 'checkConfig',
    ];

    protected $message = [
        'id.require' => 'id不能为空',
        'name.require' => '支付名称不能为空',
        'icon.require' => '支付图标不能为空',
        'sort.require' => '排序不能为空',
        'sort,number' => '排序必须是纯数字',
        'sort.max' => '排序最大不能超过五位数',
        'config.require' => '支付参数缺失',
    ];

    public function sceneGet(): PayConfigValidate
    {
        return $this->only(['id']);
    }


    /**
     * 校验支付配置记录
     * @param array $config
     * @param string $rule
     * @param array $data
     * @return string|true
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function checkConfig(array $config, string $rule, array $data): bool|string
    {
        $result = PayConfig::where('id', $data['id'])->find();
        if (empty($result)) {
            return '支付方式不存在';
        }
        if ($result['pay_way'] != PayEnum::BALANCE_PAY && !isset($config)) {
            return '支付配置不能为空';
        }

        if ($result['pay_way'] == PayEnum::WECHAT_PAY) {
            if (empty($config['interface_version'])) {
                return '微信支付接口版本不能为空';
            }
            if (empty($config['merchant_type'])) {
                return '商户类型不能为空';
            }
            if (empty($config['mch_id'])) {
                return '微信支付商户号不能为空';
            }
            if (empty($config['pay_sign_key'])) {
                return '商户API密钥不能为空';
            }
            if (empty($config['apiclient_cert'])) {
                return '微信支付证书不能为空';
            }
            if (empty($config['apiclient_key'])) {
                return '微信支付证书密钥不能为空';
            }
        }
        if ($result['pay_way'] == PayEnum::ALI_PAY) {
            if (empty($config['mode'])) {
                return '模式不能为空';
            }
            if (empty($config['merchant_type'])) {
                return '商户类型不能为空';
            }
            if (empty($config['app_id'])) {
                return '应用ID不能为空';
            }
            if (empty($config['private_key'])) {
                return '应用私钥不能为空';
            }
            if (empty($config['ali_public_key'])) {
                return '支付宝公钥不能为空';
            }
        }
        return true;
    }


    /**
     * 校验支付名
     * @param string $value
     * @param string $rule
     * @param array $data
     * @return string|true
     * @author LZH
     * @date 2025/2/19
     */
    public function checkName(string $value, string $rule, array $data): bool|string
    {
        $result = PayConfig::where('name', $value)
            ->where('id', '<>', $data['id'])
            ->findOrEmpty();
        if (!$result->isEmpty()) {
            return '支付名称已存在';
        }
        return true;
    }
}