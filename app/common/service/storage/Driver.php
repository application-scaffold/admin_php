<?php
declare(strict_types=1);

namespace app\common\service\storage;

use think\Exception;

/**
 * 存储模块驱动
 * @class Driver
 * @package app\common\service\storage
 * @author LZH
 * @date 2025/2/19
 */
class Driver
{
    private array $config;    // upload 配置
    private object $engine;    // 当前存储引擎类

    /**
     * 构造方法
     * Driver constructor.
     * @param array $config
     * @param string|null $storage 指定存储方式，如不指定则为系统默认
     * @throws Exception
     */
    public function __construct(array $config, string $storage = null)
    {
        $this->config = $config;
        $this->engine = $this->getEngineClass($storage);
    }

    /**
     * 设置上传的文件信息
     * @param string $name
     * @return mixed
     */
    public function setUploadFile(string $name = 'iFile'): mixed
    {
        return $this->engine->setUploadFile($name);
    }

    /**
     * 设置上传的文件信息
     * @param string $filePath
     * @return mixed
     */
    public function setUploadFileByReal(string $filePath): mixed
    {
        return $this->engine->setUploadFileByReal($filePath);
    }

    /**
     * 执行文件上传
     * @param string $save_dir (保存路径)
     * @return mixed
     */
    public function upload(string $save_dir): mixed
    {
        return $this->engine->upload($save_dir);
    }

    /**
     * 抓取网络资源
     * @param string $url
     * @param string $key
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function fetch(string $url, string $key): mixed
    {
        return $this->engine->fetch($url, $key);
    }

    /**
     * 执行文件删除
     * @param string $fileName
     * @return mixed
     */
    public function delete(string $fileName): mixed
    {
        return $this->engine->delete($fileName);
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError(): string
    {
        return $this->engine->getError();
    }

    /**
     * 获取文件路径
     * @return string
     */
    public function getFileName(): string
    {
        return $this->engine->getFileName();
    }

    /**
     * 返回文件信息
     * @return mixed
     */
    public function getFileInfo(): mixed
    {
        return $this->engine->getFileInfo();
    }

    /**
     * 获取当前的存储引擎
     * @param string|null $storage 指定存储方式，如不指定则为系统默认
     * @return mixed
     * @throws Exception
     */
    private function getEngineClass(string $storage = null): mixed
    {
        $engineName = is_null($storage) ? $this->config['default'] : $storage;
        $classSpace = __NAMESPACE__ . '\\engine\\' . ucfirst($engineName);

        if (!class_exists($classSpace)) {
            throw new Exception('未找到存储引擎类: ' . $engineName);
        }
        if($engineName == 'local') {
            return new $classSpace();
        }
        return new $classSpace($this->config['engine'][$engineName]);
    }

}
