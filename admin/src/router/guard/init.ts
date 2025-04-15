import type { Router } from 'vue-router'

import useAppStore from '@/stores/modules/app'

/**
 * 创建初始化路由守卫
 * 该函数会在路由跳转前检查全局配置是否已加载，如果未加载则从服务器获取配置并设置网站的favicon。
 *
 * @param {Router} router - Vue Router实例，用于注册路由守卫
 */
export default function createInitGuard(router: Router) {
    // 注册一个全局前置守卫，在每次路由跳转之前执行
    router.beforeEach(async () => {
        const appStore = useAppStore()

        // 如果全局配置为空，则从服务器获取配置
        if (Object.keys(appStore.config).length == 0) {
            // 调用store方法获取全局配置
            const data: any = await appStore.getConfig()

            // 设置网站的favicon
            let favicon: HTMLLinkElement = document.querySelector('link[rel="icon"]')!
            if (favicon) {
                // 如果页面中已有favicon标签，则更新其href属性
                favicon.href = data.web_favicon
            }
            // 创建一个新的favicon标签并添加到文档头部
            favicon = document.createElement('link')
            favicon.rel = 'icon'
            favicon.href = data.web_favicon
            document.head.appendChild(favicon)
        }
    })
}
