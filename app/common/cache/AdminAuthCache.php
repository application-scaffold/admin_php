<?php

namespace app\common\cache;

use app\adminapi\logic\auth\AuthLogic;

/**
 * 管理员权限缓存
 * @class AdminAuthCache
 * @package app\common\cache
 * @author LZH
 * @date 2025/2/18
 */
class AdminAuthCache extends BaseCache
{
    // 缓存前缀
    private $prefix = 'admin_auth_';
    // 权限配置列表
    private $authConfigList = [];
    // 权限文件MD5的缓存键
    private $cacheMd5Key = '';
    // 全部权限的缓存键
    private $cacheAllKey = '';
    // 管理员的权限缓存键
    private $cacheUrlKey = '';
    // 权限文件的MD5值
    private $authMd5 = '';
    // 管理员ID
    private $adminId = '';

    /**
     * 构造函数
     * @param string $adminId 管理员ID
     */
    public function __construct($adminId = '')
    {
        parent::__construct();

        // 初始化管理员ID
        $this->adminId = $adminId;
        // 获取全部权限配置
        $this->authConfigList = AuthLogic::getAllAuth();
        // 计算权限配置文件的MD5值
        $this->authMd5 = md5(json_encode($this->authConfigList));

        // 设置缓存键
        $this->cacheMd5Key = $this->prefix . 'md5';
        $this->cacheAllKey = $this->prefix . 'all';
        $this->cacheUrlKey = $this->prefix . 'url_' . $this->adminId;

        // 获取缓存中的权限文件MD5值和全部权限
        $cacheAuthMd5 = $this->get($this->cacheMd5Key);
        $cacheAuth = $this->get($this->cacheAllKey);

        // 如果权限配置文件已修改或缓存为空，清理缓存
        if ($this->authMd5 !== $cacheAuthMd5 || empty($cacheAuth)) {
            $this->deleteTag();
        }
    }

    /**
     * 获取管理员的权限URI
     * @return array|mixed 管理员的权限URI列表
     * @author LZH
     * @date 2025/2/18
     */
    public function getAdminUri()
    {
        // 从缓存中获取管理员的权限URI
        $urisAuth = $this->get($this->cacheUrlKey);
        if ($urisAuth) {
            return $urisAuth;
        }

        // 获取管理员角色关联的菜单ID（菜单或权限）
        $urisAuth = AuthLogic::getAuthByAdminId($this->adminId);
        if (empty($urisAuth)) {
            return [];
        }

        // 将权限URI保存到缓存，并设置过期时间为3600秒
        $this->set($this->cacheUrlKey, $urisAuth, 3600);

        // 返回权限URI
        return $urisAuth;
    }

    /**
     * 获取全部权限URI
     * @return array|mixed 全部权限URI列表
     * @author LZH
     * @date 2025/2/18
     */
    public function getAllUri()
    {
        // 从缓存中获取全部权限URI
        $cacheAuth = $this->get($this->cacheAllKey);
        if ($cacheAuth) {
            return $cacheAuth;
        }

        // 获取全部权限配置
        $authList = AuthLogic::getAllAuth();

        // 将权限文件MD5值和全部权限保存到缓存
        $this->set($this->cacheMd5Key, $this->authMd5);
        $this->set($this->cacheAllKey, $authList);

        // 返回全部权限URI
        return $authList;
    }

    /**
     * 清理管理员权限缓存
     * @return bool 清理成功返回true
     * @author LZH
     * @date 2025/2/18
     */
    public function clearAuthCache()
    {
        // 清理管理员的URL缓存
        return $this->clear($this->cacheUrlKey);
    }
}
