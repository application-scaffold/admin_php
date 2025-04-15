import type { Router } from 'vue-router'

// 使用 Vite 特有的 import.meta.glob 方法动态导入当前目录下所有 .ts 文件。eager: true 参数表示立即加载所有匹配的模块，而非默认的懒加载模式
const modules = import.meta.glob('./*.ts', { eager: true })

/**
 * 注册路由守卫
 * 该函数遍历所有模块，并调用每个模块的默认导出函数（如果存在），以注册路由守卫
 * 主要目的是为了增强系统的可扩展性，通过动态加载各个模块的路由守卫逻辑，避免了硬编码
 *
 * @param {Router} router - 路由器实例，用于注册路由守卫
 */
export function registerRouteGuard(router: Router) {
    // 遍历所有模块
    Object.keys(modules).forEach((key) => {
        // 获取当前模块的默认导出函数
        const fn = (modules[key] as any).default
        // 如果当前模块的默认导出是一个函数，则调用它，并传入路由器实例
        if (typeof fn === 'function') {
            fn(router)
        }
    })
}
