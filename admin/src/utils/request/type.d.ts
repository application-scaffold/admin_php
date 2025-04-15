import 'axios'

import type { AxiosRequestConfig, AxiosResponse, InternalAxiosRequestConfig } from 'axios'

declare module 'axios' {
    // 扩展 AxiosRequestConfig 接口，添加自定义属性
    // 扩展 RouteMeta
    interface AxiosRequestConfig {
        retryCount?: number // 重试次数
        axiosHooks?: AxiosHooks // 自定义钩子函数
        requestOptions: RequestOptions // 请求选项
    }
}

/**
 * 定义请求选项接口
 */
export interface RequestOptions {
    isParamsToData: boolean // 是否将params视为data参数，仅限post请求
    isReturnDefaultResponse: boolean // 是否返回默认的响应
    isTransformResponse: boolean // 需要对返回数据进行处理
    urlPrefix: string // 接口拼接地址
    ignoreCancelToken: boolean // 忽略重复请求
    withToken: boolean // 是否携带token
    isOpenRetry: boolean // 开启请求超时重新发起请求机制
    retryCount: number // 重新请求次数
}

/**
 * 定义Axios钩子函数接口
 */
export interface AxiosHooks {
    /**
     * 请求拦截器钩子函数
     * 在请求发送之前进行一些处理，如添加token、处理params等。
     * @param config - Axios请求配置
     * @returns 处理后的请求配置
     */
    requestInterceptorsHook?: (
        config: AxiosRequestConfig
    ) => InternalAxiosRequestConfig | AxiosRequestConfig
    /**
     * 请求拦截器错误处理钩子函数
     * 在请求拦截器发生错误时进行处理。
     * @param error - 错误对象
     * @returns 错误对象
     */
    requestInterceptorsCatchHook?: (error: Error) => void
    /**
     * 响应拦截器钩子函数
     * 在接收到响应后进行一些处理，如根据响应码处理不同的逻辑。
     * @param response - Axios响应对象
     * @returns 处理后的响应数据
     */
    responseInterceptorsHook?: (
        response: AxiosResponse<RequestData<T>>
    ) => AxiosResponse<RequestData> | RequestData | T
    /**
     * 响应拦截器错误处理钩子函数
     * 在响应拦截器发生错误时进行处理。
     * @param error - 错误对象
     * @returns 错误对象
     */
    responseInterceptorsCatchHook?: (error: AxiosError) => void
}

/**
 * 定义请求数据接口
 */
export interface RequestData<T = any> {
    code: number // 响应码
    data: T // 响应数据
    msg: string // 响应消息
    show: boolean // 是否显示消息
}
