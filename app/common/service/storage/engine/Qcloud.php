<?php
declare(strict_types=1);

namespace app\common\service\storage\engine;

use Exception;
use Qcloud\Cos\Client;


/**
 * 腾讯云存储引擎 (COS)
 * @class Qcloud
 * @package app\common\service\storage\engine
 * @author LZH
 * @date 2025/2/19
 */
class Qcloud extends Server
{
    private array $config;
    private Client $cosClient;

    /**
     * 构造方法
     * Qcloud constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct();
        $this->config = $config;
        // 创建COS控制类
        $this->createCosClient();
    }

    /**
     * 创建COS控制类
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    private function createCosClient(): void
    {
        $this->cosClient = new Client([
            'region' => $this->config['region'],
            'credentials' => [
                'secretId' => $this->config['access_key'],
                'secretKey' => $this->config['secret_key'],
            ],
        ]);
    }

    /**
     * 执行上传
     * @param string $save_dir
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public function upload(string $save_dir): bool
    {
        // 上传文件
        // putObject(上传接口，最大支持上传5G文件)
        try {
            $result = $this->cosClient->putObject([
                'Bucket' => $this->config['bucket'],
                'Key' => $save_dir . '/' . $this->fileName,
                'Body' => fopen($this->getRealPath(), 'rb')
            ]);
            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 抓取远程资源(最大支持上传5G文件)
     * @param string $url
     * @param string|null $key
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public function fetch(string $url, string $key=null): bool
    {
        try {
            $this->cosClient->putObject([
                'Bucket' => $this->config['bucket'],
                'Key'    => $key,
                'Body'   => fopen($url, 'rb')
            ]);
            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除文件
     * @param string $fileName
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public function delete(string $fileName): bool
    {
        try {
            $this->cosClient->deleteObject(array(
                'Bucket' => $this->config['bucket'],
                'Key' => $fileName
            ));
            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 返回文件路径
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

}
