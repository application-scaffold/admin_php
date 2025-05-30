<?php
declare(strict_types=1);

namespace app\common\service\sms\engine;

use AlibabaCloud\Client\AlibabaCloud;


/**
 * 阿里云短信
 * @class AliSms
 * @package app\common\service\sms\engine
 * @author LZH
 * @date 2025/2/19
 */
class AliSms
{
    protected ?string $error = null;
    protected array $config;
    protected string $mobile;
    protected string $templateId;
    protected string $templateParams;

    public function __construct($config)
    {
        if(empty($config)) {
            $this->error = '请联系管理员配置参数';
            return false;
        }
        $this->config = $config;
    }


    /**
     * 设置手机号
     * @param string $mobile
     * @return $this
     * @author LZH
     * @date 2025/2/19
     */
    public function setMobile(string $mobile): static
    {
        $this->mobile = $mobile;
        return $this;
    }


    /**
     * 设置模板id
     * @param string $templateId
     * @return $this
     * @author LZH
     * @date 2025/2/19
     */
    public function setTemplateId(string $templateId): static
    {
        $this->templateId = $templateId;
        return $this;
    }


    /**
     * 设置模板参数
     * @param array $templateParams
     * @return $this
     * @author LZH
     * @date 2025/2/19
     */
    public function setTemplateParams(array $templateParams): static
    {
        $this->templateParams = json_encode($templateParams, JSON_UNESCAPED_UNICODE);
        return $this;
    }


    /**
     * 错误信息
     * @return string|null
     * @author LZH
     * @date 2025/2/19
     */
    public function getError(): ?string
    {
        return $this->error;
    }


    /**
     * 发送短信
     * @return array|bool
     * @author LZH
     * @date 2025/2/19
     */
    public function send(): array|bool
    {
        try {
            AlibabaCloud::accessKeyClient($this->config['app_key'], $this->config['secret_key'])
                ->regionId('cn-hangzhou')
                ->asDefaultClient();

            $result = AlibabaCloud::rpcRequest()
                ->product('Dysmsapi')
                ->host('dysmsapi.aliyuncs.com')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->options([
                    'query' => [
                        'PhoneNumbers'  => $this->mobile,            //发送手机号
                        'SignName'      => $this->config['sign'],    //短信签名
                        'TemplateCode'  => $this->templateId,     //短信模板CODE
                        'TemplateParam' => $this->templateParams,    //自定义随机数
                    ],
                ])
                ->request();

            $res = $result->toArray();
            if (isset($res['Code']) && $res['Code'] == 'OK') {
                return $res;
            }
            $message = $res['Message'] ?? $res;
            throw new \Exception('阿里云短信错误：' . $message);
        } catch(\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
}