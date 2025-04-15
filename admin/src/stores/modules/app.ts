import { defineStore } from 'pinia'

import { getConfig } from '@/api/app'

/**
 * 定义应用状态的接口，包含以下字段：
 * - config: 存储应用配置信息的对象，类型为 Record<string, any>。
 * - isMobile: 标记是否为移动端设备的布尔值。
 * - isCollapsed: 标记侧边栏是否折叠的布尔值。
 * - isRouteShow: 标记当前路由视图是否显示的布尔值。
 */
interface AppSate {
    config: Record<string, any>
    isMobile: boolean
    isCollapsed: boolean
    isRouteShow: boolean
}

/**
 * 定义一个名为 'app' 的 Pinia Store，用于管理应用级别的状态和操作。
 */
const useAppStore = defineStore({
    id: 'app', // Store 的唯一标识符
    state: (): AppSate => {
        return {
            config: {}, // 初始化为空对象，用于存储应用配置
            isMobile: true, // 默认标记为移动端设备
            isCollapsed: false, // 默认侧边栏未折叠
            isRouteShow: true // 默认路由视图显示
        }
    },
    actions: {
        /**
         * 获取图片的真实 URL。
         * 如果传入的 URL 不包含 "http"，则将其与 OSS 域名拼接；否则直接返回原 URL。
         * @param url 图片的原始路径
         * @returns 拼接后的完整图片 URL
         */
        getImageUrl(url: string) {
            return url.indexOf('http') ? `${this.config.oss_domain}${url}` : url
        },

        /**
         * 异步获取应用配置信息。
         * 调用 API 获取配置数据，并更新到 store 的 config 字段中。
         * @returns 一个 Promise 对象，成功时返回配置数据，失败时返回错误信息。
         */
        getConfig() {
            return new Promise((resolve, reject) => {
                getConfig()
                    .then((data) => {
                        this.config = data // 更新配置信息
                        resolve(data)
                    })
                    .catch((err) => {
                        reject(err)
                    })
            })
        },
        /**
         * 设置设备是否为移动端。
         * @param value 标记是否为移动端的布尔值
         */
        setMobile(value: boolean) {
            this.isMobile = value
        },
        /**
         * 切换侧边栏的折叠状态。
         * 如果传入参数 toggle，则根据其值设置折叠状态；否则取反当前状态。
         * @param toggle 可选参数，指定折叠状态的布尔值
         */
        toggleCollapsed(toggle?: boolean) {
            this.isCollapsed = toggle ?? !this.isCollapsed
        },
        /**
         * 刷新当前视图。
         * 通过先隐藏再显示路由视图的方式实现视图刷新。
         */
        refreshView() {
            this.isRouteShow = false // 隐藏路由视图
            nextTick(() => {
                this.isRouteShow = true // 在下一个 tick 中重新显示路由视图
            })
        }
    }
})

export default useAppStore
