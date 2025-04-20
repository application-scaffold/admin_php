<?php
declare(strict_types=1);

namespace app\common\cache;

/**
 * 导出缓存管理类
 * @class ExportCache
 * @package app\common\cache
 * @author LZH
 * @date 2025/2/18
 */
class ExportCache extends BaseCache
{
    // 唯一标识符，用于生成缓存目录
    protected string $uniqid = '';

    /**
     * 构造函数
     * 初始化唯一标识符
     */
    public function __construct()
    {
        parent::__construct();
        // 以微秒计的当前时间，生成一个唯一的 ID，以 tag name 为前缀
        $this->uniqid = md5(uniqid($this->tagName, true) . mt_rand());
    }

    /**
     * 获取导出文件的缓存目录
     * @return string 缓存目录路径
     * @author LZH
     * @date 2025/2/18
     */
    public function getSrc(): string
    {
        // 返回缓存目录路径，格式为：runtime/file/export/年-月/唯一标识符/
        return app()->getRootPath() . 'runtime/file/export/' . date('Y-m') . '/' . $this->uniqid . '/';
    }

    /**
     * 设置文件路径缓存
     * @param string $fileName 文件名
     * @return string 缓存键
     * @author LZH
     * @date 2025/2/18
     */
    public function setFile(string $fileName): string
    {
        // 获取缓存目录路径
        $src = $this->getSrc();
        // 生成缓存键，基于目录路径、文件名和当前时间
        $key = md5($src . $fileName) . time();
        // 将文件路径和文件名保存到缓存，设置过期时间为300秒
        $this->set($key, ['src' => $src, 'name' => $fileName], 300);
        // 返回缓存键
        return $key;
    }

    /**
     * 获取文件缓存路径
     * @param string $key 缓存键
     * @return mixed 文件缓存信息（包含路径和文件名）
     * @author LZH
     * @date 2025/2/18
     */
    public function getFile(string $key): mixed
    {
        // 根据缓存键获取文件缓存信息
        return $this->get($key);
    }
}