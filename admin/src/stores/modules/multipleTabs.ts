import { defineStore } from 'pinia'
import type {
    LocationQuery,
    RouteLocationNormalized,
    RouteParamsRaw,
    Router,
    RouteRecordName
} from 'vue-router'

import { PageEnum } from '@/enums/pageEnum'
import { isExternal } from '@/utils/validate'

/**
 * 定义一个标签页项的接口，用于描述每个标签页的基本信息。
 */
interface TabItem {
    name: RouteRecordName // 标签页对应的路由名称
    fullPath: string // 标签页的完整路径
    path: string // 标签页的路径
    title?: string // 标签页的标题（可选）
    query?: LocationQuery // 路由查询参数（可选）
    params?: RouteParamsRaw // 路由参数（可选）
}

/**
 * 定义标签页状态的接口，用于存储标签页相关的状态。
 */
interface TabsSate {
    cacheTabList: Set<string> // 缓存的组件名称集合
    tabList: TabItem[] // 当前打开的标签页列表
    tasMap: Record<string, TabItem> // 标签页映射表，key为fullPath
    indexRouteName: RouteRecordName // 默认首页的路由名称
}

/**
 * 获取指定路径在标签页列表中的索引。
 * @param fullPath - 路径
 * @param tabList - 标签页列表
 * @returns 索引值，若不存在则返回-1
 */
const getHasTabIndex = (fullPath: string, tabList: TabItem[]) => {
    return tabList.findIndex((item) => item.fullPath == fullPath)
}

/**
 * 判断当前路由是否可以添加到标签页中。
 * @param route - 当前路由对象
 * @param router - Vue Router实例
 * @returns 如果不能添加则返回true，否则返回false
 */
const isCannotAddRoute = (route: RouteLocationNormalized, router: Router) => {
    const { path, meta, name } = route
    if (!path || isExternal(path)) return true // 如果路径为空或外部链接，则不能添加
    if (meta?.hideTab) return true // 如果路由元信息中设置了隐藏标签页，则不能添加
    if (!router.hasRoute(name!)) return true // 如果路由未注册，则不能添加
    if (([PageEnum.LOGIN, PageEnum.ERROR_403] as string[]).includes(path)) {
        return true // 如果是登录页或403错误页，则不能添加
    }
    return false
}

/**
 * 查找指定路径在标签页列表中的索引。
 * @param fullPath - 路径
 * @param tabList - 标签页列表
 * @returns 索引值，若不存在则返回-1
 */
const findTabsIndex = (fullPath: string, tabList: TabItem[]) => {
    return tabList.findIndex((item) => item.fullPath === fullPath)
}

/**
 * 获取路由匹配的最后一个组件的名称。
 * @param route - 当前路由对象
 * @returns 组件名称
 */
const getComponentName = (route: RouteLocationNormalized) => {
    return route.matched.at(-1)?.components?.default?.name
}

/**
 * 获取标签页的路由参数。
 * @param tabItem - 标签页项
 * @returns 包含params、path和query的对象
 */
export const getRouteParams = (tabItem: TabItem) => {
    const { params, path, query } = tabItem
    return {
        params: params || {}, // 参数，默认为空对象
        path, // 路径
        query: query || {} // 查询参数，默认为空对象
    }
}

/**
 * 定义一个Pinia Store，用于管理多标签页的状态。
 */
const useTabsStore = defineStore({
    id: 'tabs', // Store的唯一标识符
    state: (): TabsSate => ({
        cacheTabList: new Set(), // 缓存的组件名称集合
        tabList: [], // 当前打开的标签页列表
        tasMap: {}, // 标签页映射表
        indexRouteName: '' // 默认首页的路由名称
    }),
    getters: {
        /**
         * 获取当前的标签页列表。
         * @returns 标签页列表
         */
        getTabList(): TabItem[] {
            return this.tabList
        },
        /**
         * 获取缓存的组件名称列表。
         * @returns 缓存的组件名称数组
         */
        getCacheTabList(): string[] {
            return Array.from(this.cacheTabList)
        }
    },
    actions: {
        /**
         * 设置默认首页的路由名称。
         * @param name - 路由名称
         */
        setRouteName(name: RouteRecordName) {
            this.indexRouteName = name
        },
        /**
         * 添加一个组件名称到缓存集合中。
         * @param componentName - 组件名称
         */
        addCache(componentName?: string) {
            if (componentName) this.cacheTabList.add(componentName)
        },
        /**
         * 从缓存集合中移除一个组件名称。
         * @param componentName - 组件名称
         */
        removeCache(componentName?: string) {
            if (componentName && this.cacheTabList.has(componentName)) {
                this.cacheTabList.delete(componentName)
            }
        },
        /**
         * 清空缓存集合。
         */
        clearCache() {
            this.cacheTabList.clear()
        },
        /**
         * 重置Store状态。
         */
        resetState() {
            this.cacheTabList = new Set()
            this.tabList = []
            this.tasMap = {}
            this.indexRouteName = ''
        },
        /**
         * 添加一个新的标签页。
         * @param router - Vue Router实例
         */
        addTab(router: Router) {
            const route = unref(router.currentRoute)
            const { name, query, meta, params, fullPath, path } = route
            if (isCannotAddRoute(route, router)) return // 如果路由不可添加，则直接返回
            const hasTabIndex = getHasTabIndex(fullPath!, this.tabList)
            const componentName = getComponentName(route)
            const tabItem = {
                name: name!,
                path,
                fullPath,
                title: meta?.title,
                query,
                params
            }
            this.tasMap[fullPath] = tabItem
            if (meta?.keepAlive) {
                this.addCache(componentName)
            }
            if (hasTabIndex != -1) {
                return // 如果标签页已存在，则直接返回
            }

            this.tabList.push(tabItem) // 添加新的标签页
        },
        /**
         * 移除指定路径的标签页。
         * @param fullPath - 路径
         * @param router - Vue Router实例
         */
        removeTab(fullPath: string, router: Router) {
            const { currentRoute, push } = router
            const index = findTabsIndex(fullPath, this.tabList)
            // 移除tab
            if (this.tabList.length > 1) {
                index !== -1 && this.tabList.splice(index, 1)
            }
            const componentName = getComponentName(currentRoute.value)
            this.removeCache(componentName)
            if (fullPath !== currentRoute.value.fullPath) {
                return // 如果移除的不是当前标签页，则直接返回
            }
            // 删除选中的tab
            let toTab: TabItem | null = null

            if (index === 0) {
                toTab = this.tabList[index]
            } else {
                toTab = this.tabList[index - 1]
            }

            const toRoute = getRouteParams(toTab)
            push(toRoute) // 跳转到下一个标签页
        },
        /**
         * 移除其他所有标签页，仅保留当前标签页。
         * @param route - 当前路由对象
         */
        removeOtherTab(route: RouteLocationNormalized) {
            this.tabList = this.tabList.filter((item) => item.fullPath == route.fullPath)
            const componentName = getComponentName(route)
            this.cacheTabList.forEach((name) => {
                if (componentName !== name) {
                    this.removeCache(name)
                }
            })
        },
        /**
         * 移除所有标签页并跳转到首页。
         * @param router - Vue Router实例
         */
        removeAllTab(router: Router) {
            const { push, currentRoute } = router
            const { name } = unref(currentRoute)
            if (name == this.indexRouteName) {
                this.removeOtherTab(currentRoute.value)
                return
            }
            this.tabList = []
            this.clearCache()
            push(PageEnum.INDEX) // 跳转到首页
        }
    }
})

export default useTabsStore
