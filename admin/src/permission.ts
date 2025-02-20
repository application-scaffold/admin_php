/**
 * 权限控制
 */

// 引入NProgress的CSS样式
import 'nprogress/nprogress.css'

// 引入NProgress库
import NProgress from 'nprogress'

// 引入配置文件
import config from './config'

// 引入页面枚举
import { PageEnum } from './enums/pageEnum'

// 引入路由配置和工具函数
import router, { findFirstValidRoute } from './router'

// 引入默认路由配置
import { INDEX_ROUTE, INDEX_ROUTE_NAME } from './router/routes'

// 引入多标签页状态管理
import useTabsStore from './stores/modules/multipleTabs'

// 引入用户状态管理
import useUserStore from './stores/modules/user'

// 引入清除认证信息的工具函数
import { clearAuthInfo } from './utils/auth'

// 引入验证工具函数
import { isExternal } from './utils/validate'

// 动态添加路由-使用递归进行调整-（fix: 修复之前超过3级菜单导致使用keep-alive功能无效问题
const addRoutesRecursively = (routes: any, parentPath = '') => {
    try {
        // 遍历路由数组
        routes.forEach((route: any) => {
            // 如果路由是外部链接，则不添加
            if (isExternal(route.path)) {
                return
            }

            // 拼接父路由路径和当前路由路径
            const fullPath = parentPath + route.path

            // 创建路由对象，确保每个路由都有唯一的名称
            const routerEntry = {
                ...route,
                path: fullPath,
                name: route.name || fullPath.replace(/\//g, '_').replace('_', '') // 替换斜杠为下划线，生成唯一名称
            }

            // 添加路由
            if (!route.children) {
                router.addRoute(INDEX_ROUTE_NAME, routerEntry)
            } else {
                router.addRoute(routerEntry)
            }

            // 递归处理子路由
            if (route.children && route.children.length > 0) {
                addRoutesRecursively(route.children, fullPath + '/')
            }
        })
    } catch (e) {
        console.error('Error adding routes:', e)
    }
}

// NProgress配置
NProgress.configure({ showSpinner: false })

// 定义登录路径和默认路径
const loginPath = PageEnum.LOGIN
const defaultPath = PageEnum.INDEX

// 免登录白名单
const whiteList: string[] = [PageEnum.LOGIN, PageEnum.ERROR_403]

// 路由前置守卫
router.beforeEach(async (to, from, next) => {
    // 开始 Progress Bar
    NProgress.start()

    // 设置页面标题
    document.title = to.meta.title ?? config.title

    // 获取用户状态管理实例
    const userStore = useUserStore()

    // 获取多标签页状态管理实例
    const tabsStore = useTabsStore()

    // 如果路径在白名单中，直接进入
    if (whiteList.includes(to.path)) {
        next()
    } else if (userStore.token) {
        // 获取用户信息
        const hasGetUserInfo = Object.keys(userStore.userInfo).length !== 0
        if (hasGetUserInfo) {
            // 如果已经登录且访问的是登录页，则跳转到默认页
            if (to.path === loginPath) {
                next({ path: defaultPath })
            } else {
                next()
            }
        } else {
            try {
                // 获取用户信息
                await userStore.getUserInfo()

                // 获取用户路由
                const routes = userStore.routes

                // 找到第一个有效路由
                const routeName = findFirstValidRoute(routes)

                // 没有有效路由跳转到403页面
                if (!routeName) {
                    clearAuthInfo()
                    next(PageEnum.ERROR_403)
                    return
                }

                // 设置当前路由名称
                tabsStore.setRouteName(routeName!)

                // 设置默认路由的重定向
                INDEX_ROUTE.redirect = { name: routeName }

                // 动态添加index路由
                router.addRoute(INDEX_ROUTE)

                // 动态添加其余路由
                addRoutesRecursively(routes)

                // 跳转到目标路由
                next({ ...to, replace: true })
            } catch (err) {
                // 清除认证信息并跳转到登录页
                clearAuthInfo()
                next({ path: loginPath, query: { redirect: to.fullPath } })
            }
        }
    } else {
        // 未登录则跳转到登录页
        next({ path: loginPath, query: { redirect: to.fullPath } })
    }
})

// 路由后置钩子
router.afterEach(() => {
    // 结束 Progress Bar
    NProgress.done()
})
