<?php
declare(strict_types=1);

namespace app\common\service\storage\engine;

use OSS\Http\RequestCore_Exception;
use OSS\OssClient;
use OSS\Core\OssException;


/**
 * 阿里云存储引擎 (OSS)
 * @class Aliyun
 * @package app\common\service\storage\engine
 * @author LZH
 * @date 2025/2/19
 */
class Aliyun extends Server
{
    private array $config;

    /**
     * 构造方法
     * Aliyun constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct();
        $this->config = $config;
    }

    /**
     * 执行上传
     * @param string $save_dir (保存路径)
     * @return bool
     */
    public function upload(string $save_dir): bool
    {
        try {
            $ossClient = new OssClient(
                $this->config['access_key'],
                $this->config['secret_key'],
                $this->config['domain'],
                true
            );
            $ossClient->uploadFile(
                $this->config['bucket'],
                $save_dir . '/' . $this->fileName,
                $this->getRealPath()
            );
        } catch (OssException|RequestCore_Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * 抓取远程资源
     * @param string $url
     * @param string|null $key
     * @return bool
     * @throws RequestCore_Exception
     * @author LZH
     * @date 2025/2/19
     */
    public function fetch(string $url, string $key = null): bool
    {
        try {
            $ossClient = new OssClient(
                $this->config['access_key'],
                $this->config['secret_key'],
                $this->config['domain'],
                true
            );

            $content = file_get_contents($url);
            $ossClient->putObject(
                $this->config['bucket'],
                $key,
                $content
            );
        } catch (OssException $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * 删除文件
     * @param string $fileName
     * @return bool
     * @throws RequestCore_Exception
     * @author LZH
     * @date 2025/2/19
     */
    public function delete(string $fileName): bool
    {
        try {
            $ossClient = new OssClient(
                $this->config['access_key'],
                $this->config['secret_key'],
                $this->config['domain'],
                true
            );
            $ossClient->deleteObject($this->config['bucket'], $fileName);
        } catch (OssException $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return true;
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
