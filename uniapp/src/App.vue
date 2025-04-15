<script setup lang="ts">
import { onLaunch } from '@dcloudio/uni-app'
import { useAppStore } from './stores/app'
import { useUserStore } from './stores/user'
import { useThemeStore } from './stores/theme'
import { useRoute, useRouter } from 'uniapp-router-next'
const appStore = useAppStore()
const { getUser } = useUserStore()
const { getTheme } = useThemeStore()
const router = useRouter()
const route = useRoute()

//#ifdef H5
const setH5WebIcon = () => { // 设置 h5 web icon
    const config = appStore.getWebsiteConfig
    let favicon: HTMLLinkElement = document.querySelector('link[rel="icon"]')!
    if (favicon) {
        favicon.href = config.h5_favicon
        return
    }
    favicon = document.createElement('link')
    favicon.rel = 'icon'
    favicon.href = config.h5_favicon
    document.head.appendChild(favicon)
}
//#endif

const getConfig = async () => {
    await appStore.getConfig()
    //#ifdef H5
    setH5WebIcon()
    //#endif
    const { status, page_status, page_url } = appStore.getH5Config
    //是一个 路由元信息（meta） 的自定义字段，通常用于标识某个路由是否需要以 WebView 的形式打开页面
    if (route.meta.webview) return
    //处理关闭h5渠道

    //如果 status 为 0，表示 H5 渠道关闭。
    //如果 page_status 为 1，跳转到 page_url 指定的外部链接。
    //否则，使用 router.reLaunch 跳转到 /pages/empty/empty 页面。
   //#ifdef H5
    if (status == 0) {
        if (page_status == 1) return (location.href = page_url)
        //uni.reLaunch：关闭所有页面并打开新页面（适合登录态失效场景）
        //uni.redirectTo：关闭当前页面并跳转（避免返回）
        //uni.switchTab：切换至 TabBar 页面（需在 pages.json 中配置 TabBar）
        router.reLaunch('/pages/empty/empty')
    }
    //#endif
}

//应用初始化完成时触发
//当用户首次打开应用或从后台切换到前台时，onLaunch 会被调用。
//适合在此处执行全局初始化逻辑，如获取用户信息、检查登录状态、初始化全局数据等。
onLaunch(async () => {
    getTheme()
    getConfig()
    //#ifdef H5
    setH5WebIcon()
    //#endif
    await getUser()
})
// 在 UniApp 中，默认启动页面由 pages.json 的配置顺序决定。如果未在 onLaunch 中主动跳转页面，应用会根据 pages.json 的 pages 数组中的 第一个页面 作为入口。
</script>
<style lang="scss">
//
</style>
