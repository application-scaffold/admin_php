import axios, { type AxiosRequestConfig, type Canceler } from 'axios'

// 用于存储取消请求的Map，键为请求的唯一标识，值为取消函数
const cancelerMap = new Map<string, Canceler>()

// 获取一个唯一的请求键，它由请求的 URL 和参数组成
/**
 * 获取一个唯一的请求键，它由请求的 URL、方法、参数和数据组成。
 * @param config - Axios请求配置
 * @returns 请求的唯一键
 */
function getRequestKey(config: AxiosRequestConfig): string {
    const { url, method, params, data } = config
    return [method, url, JSON.stringify(params), JSON.stringify(data)].join('&')
}

/**
 * 定义一个Axios取消请求的类
 */
export class AxiosCancel {
    private static instance?: AxiosCancel // 单例实例

    /**
     * 创建并返回单例实例
     * @returns AxiosCancel实例
     */
    static createInstance() {
        return this.instance ?? (this.instance = new AxiosCancel())
    }

    /**
     * 添加一个请求到取消请求的Map中，并设置取消令牌。
     * @param config - Axios请求配置
     */
    add(config: AxiosRequestConfig) {
        const requestKey = getRequestKey(config)
        this.remove(requestKey) // 移除已存在的相同请求
        config.cancelToken = new axios.CancelToken((cancel) => {
            if (!cancelerMap.has(requestKey)) {
                cancelerMap.set(requestKey, cancel) // 存储取消函数
            }
        })
    }

    /**
     * 从取消请求的Map中移除一个请求。
     * @param requestKey - 请求的唯一键
     */
    remove(requestKey: string) {
        if (cancelerMap.has(requestKey)) {
            const cancel = cancelerMap.get(requestKey)
            cancel && cancel(requestKey) // 调用取消函数
            cancelerMap.delete(requestKey) // 从Map中删除
        }
    }
}

// 创建AxiosCancel的单例实例
const axiosCancel = AxiosCancel.createInstance()

export default axiosCancel
