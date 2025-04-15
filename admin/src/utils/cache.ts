const cache = {
    key: 'like_admin_',

    /**
     * 设置缓存（expire 为缓存时效）
     *
     * @param {string} key - 缓存的键
     * @param {any} value - 缓存的值
     * @param {string} [expire] - 缓存的过期时间（可选）
     */
    set(key: string, value: any, expire?: string) {
        key = this.getKey(key)
        let data: any = {
            expire: expire ? this.time() + expire : '',
            value
        }

        if (typeof data === 'object') {
            data = JSON.stringify(data)
        }
        try {
            window.localStorage.setItem(key, data)
        } catch (e) {
            return null
        }
    },
    /**
     * 获取缓存
     *
     * @param {string} key - 缓存的键
     * @returns {any | null} 缓存的值，如果缓存不存在或已过期则返回 null
     */
    get(key: string) {
        key = this.getKey(key)
        try {
            const data = window.localStorage.getItem(key)
            if (!data) {
                return null
            }
            const { value, expire } = JSON.parse(data)
            if (expire && expire < this.time()) {
                window.localStorage.removeItem(key)
                return null
            }
            return value
        } catch (e) {
            return null
        }
    },
    /**
     * 获取当前时间戳（秒）
     *
     * @returns {number} 当前时间戳（秒）
     */
    time() {
        return Math.round(new Date().getTime() / 1000)
    },
    /**
     * 移除指定键的缓存
     *
     * @param {string} key - 缓存的键
     */
    remove(key: string) {
        key = this.getKey(key)
        window.localStorage.removeItem(key)
    },
    /**
     * 清除所有缓存
     */
    clear() {
        window.localStorage.clear()
    },
    /**
     * 获取完整的缓存键
     *
     * @param {string} key - 缓存的键
     * @returns {string} 完整的缓存键
     */
    getKey(key: string) {
        return this.key + key
    }
}

export default cache
