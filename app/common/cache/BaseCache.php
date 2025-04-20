<?php

declare(strict_types=1);

namespace app\common\cache;

use think\Cache;

/**
 * 缓存基础类，用于管理缓存，给缓存打标签
 * @class BaseCache
 * @package app\common\cache
 * @author LZH
 * @date 2025/2/18
 */
abstract class BaseCache extends Cache
{
    /**
     * 缓存标签
     * 通过缓存标签，ThinkPHP实现了逻辑关联的缓存数据统一管理，避免了手动维护大量缓存键的复杂性，同时提升了数据一致性和维护效率
     * 1. 修改分类后，只需清除标签对应的缓存，下次请求会自动重新生成缓存
     * 2. 页面更新时，通过标签统一清除所有模块缓存
     * @var string
     */
    protected string $tagName;

    public function __construct()
    {
        parent::__construct(app());
        $this->tagName = get_class($this); // 把继承类名称作为标签名称
    }

    /**
     * 重写父类set，自动打上标签
     * @param string $key
     * @param mixed $value
     * @param mixed $ttl
     * @return bool
     * @author LZH
     * @date 2025/2/18
     */
    public function set(string $key, mixed $value, mixed $ttl = null): bool
    {
        return $this->store()->tag($this->tagName)->set($key, $value, $ttl);
    }


    /**
     * 清除该缓存类所有缓存
     * @return bool
     * @author LZH
     * @date 2025/2/18
     */
    public function deleteTag(): bool
    {
        return $this->tag($this->tagName)->clear();
    }

}