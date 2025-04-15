import routes from 'uni-router-routes'
import { createRouter } from 'uniapp-router-next'

import { ClientEnum } from '@/enums/appEnums'
import { useUserStore } from '@/stores/user'
import { client } from '@/utils/client'
// #ifdef H5
import wechatOa from '@/utils/wechat'
// #endif
import cache from '@/utils/cache'
import { BACK_URL } from '@/enums/constantEnums'

// unplugin-uni-router 根据 pages.json 或文件目录结构自动生成路由配置，减少手动配置的工作量
const router = createRouter({
    routes: [
        ...routes,
        {
            path: '*',
            redirect() {
                return {
                    name: '404'
                }
            }
        }
    ],
    debug: import.meta.env.DEV,
    //@ts-ignore
    platform: process.env.UNI_PLATFORM,
    h5: {}
})

//多次调用 router.beforeEach 会 按调用顺序依次执行 每个全局前置守卫
//存储登陆前的页面
let isFirstEach = true
router.beforeEach(async (to, from) => {
    //保存第一次进来时的页面路径（需要登陆才能访问的页面）
    if (isFirstEach) {
        const userStore = useUserStore()
        if (!userStore.isLogin && !to.meta.white) {
            cache.set(BACK_URL, to.fullPath)
        }
        isFirstEach = false
    }
})
router.afterEach((to, from) => {
    const userStore = useUserStore()
    if (!userStore.isLogin && !to.meta.white) {
        //登录成功后，跳回前一个页面
        cache.set(BACK_URL, to.fullPath)
    }
})

// 登录拦截
router.beforeEach(async (to, from) => {
    const userStore = useUserStore();
    if (!userStore.isLogin && to.meta.auth) {
        return '/pages/login/login'
    }
})

// #ifdef H5
//用于收集微信公众号的授权的code，并清除路径上微信带的参数
router.beforeEach(async (to, form) => {
    const { code, state, scene } = to.query

    if (code && state && scene) {
        wechatOa.setAuthData({
            code,
            scene
        })
        //收集完删除路径上的参数
        delete to.query.code
        delete to.query.state
        return {
            path: to.path,
            force: true,
            navType: 'reLaunch',
            query: to.query
        }
    }
})
// #endif

// #ifdef H5
router.afterEach((to, from) => {
    setTimeout(async () => {
        // 当应用运行在微信内置浏览器（ClientEnum.OA_WEIXIN）时，需初始化微信 JS-SDK
        // 排除 WebView 页面：通过 to.meta.webview 标记的页面可能不需要微信功能（如嵌套的 H5 页面）
        if (client == ClientEnum.OA_WEIXIN && !to.meta.webview) {
            // jssdk配置
            await wechatOa.config()
        }
    })
})
// #endif
export default router
