import axios, {
    AxiosError,
    type AxiosInstance,
    type AxiosRequestConfig,
    type AxiosResponse,
    type InternalAxiosRequestConfig
} from 'axios'
import { cloneDeep, isFunction, merge } from 'lodash'

import { RequestMethodsEnum } from '@/enums/requestEnums'

import axiosCancel from './cancel'
import type { RequestData, RequestOptions } from './type'

/**
 * 自定义Axios类，用于封装Axios实例并添加额外的功能，如拦截器、取消请求和重试机制。
 */
export class Axios {
    private axiosInstance: AxiosInstance // Axios实例
    private readonly config: AxiosRequestConfig // Axios配置
    private readonly options: RequestOptions // 请求选项

    /**
     * 构造函数，初始化Axios实例并设置拦截器。
     * @param config - Axios配置
     */
    constructor(config: AxiosRequestConfig) {
        this.config = config
        this.options = config.requestOptions
        this.axiosInstance = axios.create(config)
        this.setupInterceptors()
    }

    /**
     * 获取Axios实例。
     * @returns Axios实例
     */
    getAxiosInstance() {
        return this.axiosInstance
    }

    /**
     * 设置请求和响应拦截器。
     */
    setupInterceptors() {
        if (!this.config.axiosHooks) {
            return
        }
        const {
            requestInterceptorsHook,
            requestInterceptorsCatchHook,
            responseInterceptorsHook,
            responseInterceptorsCatchHook
        } = this.config.axiosHooks
        // 请求拦截器
        this.axiosInstance.interceptors.request.use(
            (config) => {
                this.addCancelToken(config) // 添加取消令牌
                if (isFunction(requestInterceptorsHook)) {
                    // 调用请求拦截器钩子函数
                    config = requestInterceptorsHook(config) as InternalAxiosRequestConfig
                }
                return config
            },
            (err: Error) => {
                if (isFunction(requestInterceptorsCatchHook)) {
                    // 调用请求拦截器错误处理钩子函数
                    requestInterceptorsCatchHook(err)
                }
                return err
            }
        )
        this.axiosInstance.interceptors.response.use(
            (response: AxiosResponse<RequestData>) => {
                this.removeCancelToken(response.config.url!) // 移除取消令牌
                if (isFunction(responseInterceptorsHook)) {
                    // 调用响应拦截器钩子函数
                    response = responseInterceptorsHook(response)
                }
                return response
            },
            (err: AxiosError) => {
                if (isFunction(responseInterceptorsCatchHook)) {
                    responseInterceptorsCatchHook(err) // 调用响应拦截器错误处理钩子函数
                }
                if (err.code != AxiosError.ERR_CANCELED) {
                    this.removeCancelToken(err.config?.url!) // 移除取消令牌
                }

                // 处理网络错误和请求超时，进行重试
                if (err.code == AxiosError.ECONNABORTED || err.code == AxiosError.ERR_NETWORK) {
                    return new Promise((resolve) => setTimeout(resolve, 500)).then(() =>
                        this.retryRequest(err)
                    )
                }
                return Promise.reject(err)
            }
        )
    }

    /**
     * 添加取消令牌到请求配置中。
     * @param config - Axios请求配置
     */
    addCancelToken(config: AxiosRequestConfig) {
        const { ignoreCancelToken } = config.requestOptions
        !ignoreCancelToken && axiosCancel.add(config) // 如果未忽略取消令牌，则添加
    }

    /**
     * 从取消令牌Map中移除指定URL的取消令牌。
     * @param url - 请求的URL
     */
    removeCancelToken(url: string) {
        axiosCancel.remove(url)
    }

    /**
     * 重试请求。
     * @param error - Axios错误对象
     * @returns 重试后的Promise
     */
    retryRequest(error: AxiosError) {
        const config = error.config as any
        const { retryCount, isOpenRetry } = config.requestOptions

        // 如果未开启重试或请求方法为POST，则不重试
        if (!isOpenRetry || config.method?.toUpperCase() == RequestMethodsEnum.POST) {
            return Promise.reject(error)
        }

        config.retryCount = config.retryCount ?? 0

        // 如果重试次数达到上限，则拒绝请求
        if (config.retryCount >= retryCount) {
            return Promise.reject(error)
        }

        config.retryCount++ // 增加重试次数

        return this.axiosInstance.request(config) // 重新发起请求
    }

    /**
     * 发起GET请求。
     * @param config - Axios请求配置
     * @param options - 请求选项
     * @returns 请求结果的Promise
     */
    get<T = any>(
        config: Partial<AxiosRequestConfig>,
        options?: Partial<RequestOptions>
    ): Promise<T> {
        return this.request({ ...config, method: RequestMethodsEnum.GET }, options)
    }

    /**
     * 发起POST请求。
     * @param config - Axios请求配置
     * @param options - 请求选项
     * @returns 请求结果的Promise
     */
    post<T = any>(
        config: Partial<AxiosRequestConfig>,
        options?: Partial<RequestOptions>
    ): Promise<T> {
        return this.request({ ...config, method: RequestMethodsEnum.POST }, options)
    }

    /**
     * 发起请求。
     * @param config - Axios请求配置
     * @param options - 请求选项
     * @returns 请求结果的Promise
     */
    request<T = any>(
        config: Partial<AxiosRequestConfig>,
        options?: Partial<RequestOptions>
    ): Promise<any> {
        const opt: RequestOptions = merge({}, this.options, options)
        const axioxConfig: AxiosRequestConfig = {
            ...cloneDeep(config),
            requestOptions: opt
        }
        const { urlPrefix } = opt
        // 拼接请求前缀如api
        if (urlPrefix) {
            axioxConfig.url = `${urlPrefix}${config.url}`
        }
        return new Promise((resolve, reject) => {
            this.axiosInstance
                .request<any, AxiosResponse<RequestData<T>>>(axioxConfig)
                .then((res) => {
                    resolve(res)
                })
                .catch((err) => {
                    reject(err)
                })
        })
    }
}
