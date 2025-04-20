<?php
declare(strict_types=1);

namespace app\common\cache;

/**
 * 用户账号安全机制，连续输错后锁定，防止账号密码暴力破解
 * @class UserAccountSafeCache
 * @package app\common\cache
 * @author LZH
 * @date 2025/2/18
 */
class UserAccountSafeCache extends BaseCache
{

    private string $key;//缓存次数名称
    public int $minute = 15;//缓存设置为15分钟，即密码错误次数达到，锁定15分钟
    public int $count = 15;  //设置连续输错次数，即15分钟内连续输错误15次后，锁定

    public function __construct()
    {
        parent::__construct();
        $ip = \request()->ip();
        $this->key = $this->tagName . $ip;
    }

    /**
     * 记录登录错误次数
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function record(): void
    {
        if ($this->get($this->key)) {
            //缓存存在，记录错误次数
            $this->inc($this->key, 1);
        } else {
            //缓存不存在，第一次设置缓存
            $this->set($this->key, 1, $this->minute * 60);
        }
    }

    /**
     * 判断是否安全
     * @return bool
     * @author LZH
     * @date 2025/2/18
     */
    public function isSafe(): bool
    {
        $count = $this->get($this->key);
        if ($count >= $this->count) {
            return false;
        }
        return true;
    }

    /**
     * 删除该ip记录错误次数
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function relieve(): void
    {
        $this->delete($this->key);
    }

}