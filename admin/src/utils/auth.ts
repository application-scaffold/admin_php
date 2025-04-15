import { TOKEN_KEY } from '@/enums/cacheEnums'
import { resetRouter } from '@/router'
import useTabsStore from '@/stores/modules/multipleTabs'
import useUserStore from '@/stores/modules/user'

import cache from './cache'

/**
 * 从缓存中获取令牌。
 *
 * 该函数不接受任何参数。
 *
 * @returns {string | null} 如果缓存中找到令牌，则返回令牌字符串，否则返回 null。
 */
export function getToken() {
    return cache.get(TOKEN_KEY)
}

/**
 * 清除所有认证相关信息。
 *
 * 该函数重置用户和标签页存储，从缓存中移除令牌，并重置路由。
 *
 * 该函数不接受任何参数且不返回任何值。
 */
export function clearAuthInfo() {
    const userStore = useUserStore()
    const tabsStore = useTabsStore()
    userStore.resetState()
    tabsStore.resetState()
    cache.remove(TOKEN_KEY)
    resetRouter()
}
