import { merge } from 'lodash-es'
import { isFunction } from '@vue/shared'
import { HttpRequestOptions, RequestConfig, RequestOptions, UploadFileOption } from './type'
import { RequestErrMsgEnum, RequestMethodsEnum } from '@/enums/requestEnums'
import requestCancel from './cancel'

//封装 HTTP 请求核心逻辑，支持多平台（uni-app）
export default class HttpRequest {
    private readonly options: HttpRequestOptions
    constructor(options: HttpRequestOptions) {
        this.options = options // 存储全局默认配置
    }
    /**
     * @description 重新请求
     */
    retryRequest(options: RequestOptions, config: RequestConfig) {
        const { retryCount, retryTimeout } = config
        if (!retryCount || options.method?.toUpperCase() == RequestMethodsEnum.POST) {
            return Promise.reject()
        }
        // 显示加载提示
        uni.showLoading({ title: '加载中...' })
        // 初始化已重试次数
        config.hasRetryCount = config.hasRetryCount ?? 0
        // 终止条件：已达最大重试次数
        if (config.hasRetryCount >= retryCount) {
            return Promise.reject()
        }
        // 更新重试计数器
        config.hasRetryCount++
        // 重置请求拦截器（防止副作用）
        config.requestHooks.requestInterceptorsHook = (options) => options
        // 延迟后发起重试请求
        return new Promise((resolve) => setTimeout(resolve, retryTimeout))
            .then(() => this.request(options, config))
            .finally(() => uni.hideLoading()) // 隐藏提示
    }
    /**
     * @description get请求
     */
    get<T = any>(options: RequestOptions, config?: Partial<RequestConfig>): Promise<T> {
        return this.request({ ...options, method: RequestMethodsEnum.GET }, config)
    }

    /**
     * @description post请求
     */
    post<T = any>(options: RequestOptions, config?: Partial<RequestConfig>): Promise<T> {
        return this.request({ ...options, method: RequestMethodsEnum.POST }, config)
    }

    /**
     * @description 上传图片
     */
    uploadFile(options: UploadFileOption, config?: Partial<RequestConfig>) {
        // 合并配置并应用请求拦截器
        let mergeOptions: RequestOptions = merge({}, this.options.requestOptions, options)
        const mergeConfig: RequestConfig = merge({}, this.options, config)
        const { requestInterceptorsHook, responseInterceptorsHook, responseInterceptorsCatchHook } =
            mergeConfig.requestHooks || {}
        if (requestInterceptorsHook && isFunction(requestInterceptorsHook)) {
            mergeOptions = requestInterceptorsHook(mergeOptions, mergeConfig)
        }
        return new Promise((resolve, reject) => {
            // 调用 uni.uploadFile
            uni.uploadFile({
                ...mergeOptions,
                success: async (response) => { /* 处理成功响应 */
                    if (response.statusCode == 200) {
                        response.data = JSON.parse(response.data)
                        if (responseInterceptorsHook && isFunction(responseInterceptorsHook)) {
                            try {
                                response = await responseInterceptorsHook(response, mergeConfig)
                                resolve(response)
                            } catch (error) {
                                reject(error)
                            }
                            return
                        }
                        resolve(response)
                    }
                },
                fail: async (err) => { /* 处理失败 */
                    if (
                        responseInterceptorsCatchHook &&
                        isFunction(responseInterceptorsCatchHook)
                    ) {
                        reject(await responseInterceptorsCatchHook(mergeOptions, err))
                        return
                    }
                    reject(err)
                }
            })
        })
    }
    /**
     * @description 请求函数
     */
    async request(options: RequestOptions, config?: Partial<RequestConfig>): Promise<any> {
        let mergeOptions: RequestOptions = merge({}, this.options.requestOptions, options)
        const mergeConfig: RequestConfig = merge({}, this.options, config)
        const { requestInterceptorsHook, responseInterceptorsHook, responseInterceptorsCatchHook } =
            mergeConfig.requestHooks || {}
        // 调用请求拦截器钩子
        if (requestInterceptorsHook && isFunction(requestInterceptorsHook)) {
            mergeOptions = requestInterceptorsHook(mergeOptions, mergeConfig)
        }
        return new Promise((resolve, reject) => {
            // 1. 请求成功时，执行顺序为：success → complete
            // 2. 请求失败时，执行顺序为：fail → complete
            // 3. 中断请求时，会触发 complete
            const requestTask = uni.request({
                ...mergeOptions,
                async success(response) {
                    // 调用响应拦截器钩子
                    if (responseInterceptorsHook && isFunction(responseInterceptorsHook)) {
                        try {
                            response = await responseInterceptorsHook(response, mergeConfig)
                            resolve(response)
                        } catch (error) {
                            reject(error)
                        }
                        return
                    }
                    resolve(response)
                },
                fail: async (err) => {
                    // 超时重试
                    if (err.errMsg == RequestErrMsgEnum.TIMEOUT) {
                        this.retryRequest(mergeOptions, mergeConfig)
                            .then((res) => resolve(res))
                            .catch((err) => reject(err))
                        return
                    }

                    // 错误拦截器钩子
                    if (
                        responseInterceptorsCatchHook &&
                        isFunction(responseInterceptorsCatchHook)
                    ) {
                        reject(await responseInterceptorsCatchHook(mergeOptions, err))
                        return
                    }
                    reject(err)
                },
                complete(err) { /* 清理请求队列 */
                    if (err.errMsg !== RequestErrMsgEnum.ABORT) {
                        requestCancel.remove(options.url)
                    }
                }
            })
            // 加入取消队列
            const { ignoreCancel } = mergeConfig
            !ignoreCancel && requestCancel.add(options.url, requestTask)
        })
    }
}
