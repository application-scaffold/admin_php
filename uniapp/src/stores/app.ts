import { defineStore } from 'pinia'
import { getConfig } from '@/api/app'

interface AppSate {
    // Record<string, any> 用于定义一个 键为字符串、值为任意类型 的对象类型
    config: Record<string, any>
}
export const useAppStore = defineStore({
    id: 'appStore',
    state: (): AppSate => ({
        config: {}
    }),
    getters: {
        getWebsiteConfig: (state) => state.config.website || {},
        getLoginConfig: (state) => state.config.login || {},
        getTabbarConfig: (state) => state.config.tabbar || [],
        getStyleConfig: (state) => state.config.style || {},
        getH5Config: (state) => state.config.webPage || {},
        getCopyrightConfig: (state) => state.config.copyright || [],
    },
    actions: {
        getImageUrl(url: string) {
            return url.indexOf('http') ? `${this.config.domain}${url}` : url
        },
        async getConfig() {
            const data = await getConfig()
            this.config = data
        }
    }
})
