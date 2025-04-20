<?php
declare(strict_types=1);

namespace app\common\cache;

/**
 * 扫码登录缓存管理类
 * @class WebScanLoginCache
 * @package app\common\cache
 * @author LZH
 * @date 2025/2/18
 */
class WebScanLoginCache extends BaseCache
{
    // 缓存前缀，用于区分扫码登录相关的缓存
    private string $prefix = 'web_scan_';

    /**
     * 获取扫码登录状态标记
     * @param string $state 扫码登录的状态标识
     * @return mixed 扫码登录状态信息
     * @author LZH
     * @date 2025/2/18
     */
    public function getScanLoginState(string $state): mixed
    {
        // 根据状态标识从缓存中获取扫码登录状态
        return $this->get($this->prefix . $state);
    }

    /**
     * 设置扫码登录状态
     * @param string $state 扫码登录的状态标识
     * @return mixed 设置后的扫码登录状态信息
     * @author LZH
     * @date 2025/2/18
     */
    public function setScanLoginState(string $state): mixed
    {
        // 将扫码登录状态保存到缓存，并设置过期时间为600秒（10分钟）
        $this->set($this->prefix . $state, $state, 600);
        // 返回设置后的扫码登录状态信息
        return $this->getScanLoginState($state);
    }

    /**
     * 删除扫码登录状态缓存
     * @param string $state 扫码登录的状态标识
     * @return bool 删除成功返回true
     * @author LZH
     * @date 2025/2/18
     */
    public function deleteLoginState(string $state): bool
    {
        // 根据状态标识删除扫码登录状态缓存
        return $this->delete($this->prefix . $state);
    }
}