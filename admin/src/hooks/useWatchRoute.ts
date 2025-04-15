import type { RouteLocationNormalizedLoaded } from 'vue-router'

/**
 * 监视路由变化并执行回调函数的钩子
 *
 * 该钩子用于在路由发生变化时执行指定的回调函数，以便组件可以根据当前路由进行相应的操作
 * 它利用了Vue的watch功能来监听路由的变化，并在路由变化时立即执行回调函数
 *
 * @param callback 当路由变化时要执行的回调函数，接收当前的路由信息作为参数
 * @returns 返回一个包含当前路由信息的对象
 */
export function useWatchRoute(callback: (route: RouteLocationNormalizedLoaded) => void) {
    // 获取当前的路由信息
    const route = useRoute()
    // 监视路由变化，当路由变化时执行回调函数
    watch(
        route,
        () => {
            callback(route)
        },
        {
            // 立即执行一次回调函数，以便在组件挂载时进行必要的操作
            immediate: true
        }
    )

    // 返回包含当前路由信息的对象
    return {
        route
    }
}
