import { createRouter, createWebHistory, type RouteRecordRaw, RouterView } from 'vue-router'

import { MenuEnum } from '@/enums/appEnums'
import useUserStore from '@/stores/modules/user'
import { isExternal } from '@/utils/validate'

import { constantRoutes, INDEX_ROUTE_NAME, LAYOUT } from './routes'

// 匹配views里面所有的.vue文件，动态引入
const modules = import.meta.glob('/src/views/**/*.vue')

/**
 * 获取模块的关键字列表
 * 此函数旨在从模块对象中提取关键字，通过移除路径和文件扩展名，简化模块名称
 * 主要目的是为了提供一个简化且统一的模块关键字列表，便于后续处理和引用
 *
 * @returns {string[]} 返回一个字符串数组，包含了简化后的模块关键字
 */
export function getModulesKey() {
    // 对模块对象的键进行映射，移除每个键中的特定路径和文件扩展名
    return Object.keys(modules).map((item) => item.replace('/src/views/', '').replace('.vue', ''))
}

// 过滤路由所需要的数据
/**
 * 过滤异步路由并创建新的路由记录
 *
 * 此函数通过接收一个路由配置数组，递归地创建一个新的路由记录数组
 * 它主要用于处理异步路由配置，将它们转换为实际的路由记录格式
 *
 * @param routes - 要处理的路由配置数组
 * @param firstRoute - 标志表示是否是第一个路由，默认为true
 * @returns 返回一个新的路由记录数组
 */
export function filterAsyncRoutes(routes: any[], firstRoute = true) {
    // 对每个路由配置进行处理，创建对应的路由记录
    return routes.map((route) => {
        // 根据当前路由配置和是否为第一个路由的标志，创建路由记录
        const routeRecord = createRouteRecord(route, firstRoute)
        // 如果当前路由配置有子路由，则递归地处理子路由
        if (route.children != null && route.children && route.children.length) {
            // 为当前路由记录的子路由创建新的路由记录数组
            routeRecord.children = filterAsyncRoutes(route.children, false)
        }
        // 返回创建的路由记录
        return routeRecord
    })
}

// 创建一条路由记录
/**
 * 创建路由记录
 *
 * 根据给定的路由信息和是否为第一个路由的标志，生成一个路由记录对象此函数解释了如何根据菜单类型和属性来构建路由配置
 *
 * @param route - 路由信息对象，包含路径、显示状态、缓存状态等
 * @param firstRoute - 布尔值，指示是否为第一个路由，影响路由路径和组件的设置
 * @returns 返回一个RouteRecordRaw对象，包含路径、名称、元数据和组件信息
 */
export function createRouteRecord(route: any, firstRoute: boolean): RouteRecordRaw {
    // 根据路径是否外部、是否第一个路由等条件构建路由记录的路径和名称
    //@ts-ignore
    const routeRecord: RouteRecordRaw = {
        path: isExternal(route.paths) ? route.paths : firstRoute ? `/${route.paths}` : route.paths,
        name: Symbol(route.paths),
        // 根据路由信息填充元数据，包括隐藏状态、缓存状态、标题、权限、查询参数、图标、类型和激活菜单
        meta: {
            hidden: !route.is_show,
            keepAlive: !!route.is_cache,
            title: route.name,
            perms: route.perms,
            query: route.params,
            icon: route.icon,
            type: route.type,
            activeMenu: route.selected
        }
    }
    // 根据菜单类型设置路由组件
    switch (route.type) {
        case MenuEnum.CATALOGUE:
            // 如果是目录类型，根据是否为第一个路由决定使用LAYOUT还是RouterView
            routeRecord.component = firstRoute ? LAYOUT : RouterView
            // 如果没有子路由，则使用RouterView
            if (!route.children) {
                routeRecord.component = RouterView
            }
            break
        case MenuEnum.MENU:
            // 如果是菜单类型，动态加载指定的组件
            routeRecord.component = loadRouteView(route.component)
            break
    }
    // 返回构建好的路由记录
    return routeRecord
}

// 动态加载组件
/**
 * 动态加载路由视图组件
 *
 * 该函数通过组件名称来动态加载对应的路由视图组件主要用于实现路由组件的按需加载
 * 它通过查找已知的模块来获取指定组件，如果找不到则抛出错误，并在捕获错误时返回一个默认的RouterView组件
 *
 * @param component 组件名称，用于查找对应的路由视图组件
 * @returns 返回找到的组件，如果找不到则返回默认的RouterView组件
 */
export function loadRouteView(component: string) {
    try {
        // 尝试找到包含指定组件的模块键值
        const key = Object.keys(modules).find((key) => {
            return key.includes(`/${component}.vue`)
        })
        // 如果找到对应的模块键值，则返回该模块
        if (key) {
            return modules[key]
        }
        // 如果没有找到对应的模块，则抛出错误
        throw Error(`找不到组件${component}，请确保组件路径正确`)
    } catch (error) {
        // 捕获错误并输出错误信息
        console.error(error)
        // 返回默认的RouterView组件
        return RouterView
    }
}

// 找到第一个有效的路由
/**
 * 递归查找第一个有效的路由名称
 * 该函数旨在从一个路由配置数组中，找到第一个不隐藏且类型符合菜单的路由名称
 * 主要用于初始化或默认路由选择的场景
 *
 * @param routes 路由配置数组，包含多个路由记录每个记录都有可能是带有children的父路由
 * @returns 返回第一个满足条件的路由名称，如果不存在则返回undefined
 */
export function findFirstValidRoute(routes: RouteRecordRaw[]): string | undefined {
    // 遍历每个路由以查找满足条件的路由
    for (const route of routes) {
        // 检查当前路由是否为菜单类型，且不隐藏，且路径不是外部链接
        if (route.meta?.type == MenuEnum.MENU && !route.meta?.hidden && !isExternal(route.path)) {
            // 如果条件满足，则返回当前路由的名称
            return route.name as string
        }
        // 如果当前路由有子路由，则递归查找子路由中的有效路由
        if (route.children) {
            const name = findFirstValidRoute(route.children)
            // 如果在子路由中找到了有效路由，则返回该路由的名称
            if (name) {
                return name
            }
        }
    }
    // 如果所有路由都不满足条件，则返回undefined
}

//通过权限字符查询路由路径
/**
 * 根据权限标识获取路由路径
 *
 * 此函数旨在通过权限标识（perms）来获取对应的路由路径它通过查找路由配置来实现这一点
 * 如果找到了匹配的路由，则返回该路由的路径；如果没有找到匹配的路由，则返回空字符串
 *
 * @param perms 权限标识，用于查找对应的路由
 * @returns 返回找到的路由路径或空字符串
 */
export function getRoutePath(perms: string) {
    // 获取当前路由器实例，如果没有获取到，则使用默认的router实例
    const routerObj = useRouter() || router
    // 从路由器实例中获取所有路由，查找具有指定权限标识的路由
    // 如果找到匹配的路由，则返回该路由的路径；否则返回空字符串
    return routerObj.getRoutes().find((item) => item.meta?.perms == perms)?.path || ''
}

// 重置路由
/**
 * 重置路由配置
 *
 * 此函数旨在移除当前应用中的所有动态路由，恢复到初始路由状态它首先移除一个名为INDEX_ROUTE_NAME的路由，
 * 然后遍历用户路由列表，对每个路由进行移除操作，如果该路由在当前路由配置中存在的话
 * 这通常在用户登出或应用初始化时使用，以确保路由配置不会残留之前用户的状态
 */
export function resetRouter() {
    // 移除首页路由，这是应用中通常存在的一个固定路由
    router.removeRoute(INDEX_ROUTE_NAME)
    // 获取当前用户的所有路由配置
    const { routes } = useUserStore()
    // 遍历用户路由列表，对每个路由执行移除操作
    routes.forEach((route) => {
        const name = route.name
        // 检查路由名称是否存在，并确认当前路由配置中包含此名称的路由，如果都满足，则移除该路由
        if (name && router.hasRoute(name)) {
            router.removeRoute(name)
        }
    })
}

// 创建 Vue 路由实例
// 使用 Vue Router 的 createRouter 方法创建一个路由对象，配置历史模式和基础路由。
// history: 使用 Web History API，基于 import.meta.env.BASE_URL 配置基础路径。
// routes: 初始化时加载 constantRoutes，这些是应用中固定的、不需要动态加载的路由。
const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: constantRoutes
})

export default router
