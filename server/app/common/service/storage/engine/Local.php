<?php

namespace app\common\service\storage\engine;

/**
 * 本地文件驱动
 * @class Local
 * @package app\common\service\storage\engine
 * @author LZH
 * @date 2025/2/19
 */
class Local extends Server
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 上传
     * @param $save_dir 保存路径
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public function upload($save_dir)
    {
        // 验证文件并上传
        $info = $this->file->move($save_dir, $this->fileName);
        if (empty($info)) {
            $this->error = $this->file->getError();
            return false;
        }
        return true;
    }

    public function fetch($url, $key=null) {}

    /**
     * 删除文件
     * @param $fileName
     * @return bool|mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function delete($fileName)
    {
        $check = strpos($fileName, '/');
        if ($check !== false && $check == 0) {
            // 文件所在目录
            $fileName = substr_replace($fileName,"",0,1);
        }
        $filePath = public_path() . "{$fileName}";
        return !file_exists($filePath) ?: unlink($filePath);
    }

    /**
     * 返回文件路径
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function getFileName()
    {
        return $this->fileName;
    }
}
