<?php
declare(strict_types=1);

namespace app\common\service\storage\engine;

use think\file\UploadedFile;
use think\Request;
use think\Exception;


/**
 * 存储引擎抽象类
 * @class Server
 * @package app\common\service\storage\engine
 * @author LZH
 * @date 2025/2/19
 */
abstract class Server
{
    protected UploadedFile $file;
    protected string $error;
    protected string $fileName;
    protected array $fileInfo;

    // 是否为内部上传
    protected bool $isInternal = false;

    /**
     * 构造函数
     * Server constructor.
     */
    protected function __construct()
    {
    }

    /**
     * 设置上传的文件信息
     * @param string $name
     * @return void
     * @throws Exception
     * @author LZH
     * @date 2025/2/19
     */
    public function setUploadFile(string $name): void
    {
        // 接收上传的文件
        $this->file = request()->file($name);
        if (empty($this->file)) {
            throw new Exception('未找到上传文件的信息');
        }

        // 校验上传文件后缀
        $limit = array_merge(config('project.file_image'), config('project.file_video'), config('project.file_file'));
        if (!in_array(strtolower($this->file->extension()), $limit)) {
            throw new Exception('不允许上传' . $this->file->extension() . '后缀文件');
        }

        // 文件信息
        $this->fileInfo = [
            'ext'      => $this->file->extension(),
            'size'     => $this->file->getSize(),
            'mime'     => $this->file->getMime(),
            'name'     => $this->file->getOriginalName(),
            'realPath' => $this->file->getRealPath(),
        ];
        // 生成保存文件名
        $this->fileName = $this->buildSaveName();
    }

    /**
     * 设置上传的文件信息
     * @param string $filePath
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public function setUploadFileByReal(string $filePath): void
    {
        // 设置为系统内部上传
        $this->isInternal = true;
        // 文件信息
        $this->fileInfo = [
            'name' => basename($filePath),
            'size' => filesize($filePath),
            'tmp_name' => $filePath,
            'error' => 0,
        ];
        // 生成保存文件名
        $this->fileName = $this->buildSaveName();
    }

    /**
     * 抓取网络资源
     * @param string $url
     * @param string $key
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    abstract protected function fetch(string $url, string $key): bool;

    /**
     * 文件上传
     * @param string $save_dir
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    abstract protected function upload(string $save_dir): bool;

    /**
     * 文件删除
     * @param string $fileName
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    abstract protected function delete(string $fileName): bool;

    /**
     * 返回上传后文件路径
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    abstract public function getFileName(): string;

    /**
     * 返回文件信息
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function getFileInfo(): array
    {
        return $this->fileInfo;
    }

    protected function getRealPath(): string
    {
        return $this->fileInfo['realPath'];
    }

    /**
     * 返回错误信息
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * 生成保存文件名
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    private function buildSaveName(): string
    {
        // 要上传图片的本地路径
        $realPath = $this->getRealPath();
        // 扩展名
        $ext = pathinfo($this->getFileInfo()['name'], PATHINFO_EXTENSION);
        // 自动生成文件名
        return date('YmdHis') . substr(md5($realPath), 0, 5)
            . str_pad((string)rand(0, 9999), 4, '0', STR_PAD_LEFT) . ".{$ext}";
    }

}
