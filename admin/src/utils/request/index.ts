import { AxiosError, type AxiosRequestConfig } from 'axios'
import { merge } from 'lodash'
import NProgress from 'nprogress'

import configs from '@/config'
import { PageEnum } from '@/enums/pageEnum'
import { ContentTypeEnum, RequestCodeEnum, RequestMethodsEnum } from '@/enums/requestEnums'
import router from '@/router'

import { clearAuthInfo, getToken } from '../auth'
import feedback from '../feedback'
import { Axios } from './axios'
import type { AxiosHooks } from './type'

// 处理axios的钩子函数
const axiosHooks: AxiosHooks = {
    /**
     * 请求拦截器钩子函数
     * 在请求发送之前进行一些处理，如添加token、处理params等。
     * @param config - Axios请求配置
     * @returns 处理后的请求配置
     */
    requestInterceptorsHook(config) {
        NProgress.start() // 开始进度条
        const { withToken, isParamsToData } = config.requestOptions
        const params = config.params || {}
        const headers = config.headers || {}

        // 添加token到请求头
        if (withToken) {
            const token = getToken()
            headers.token = token
        }
        // POST请求下如果无data，则将params视为data
        if (
            isParamsToData &&
            !Reflect.has(config, 'data') &&
            config.method?.toUpperCase() === RequestMethodsEnum.POST
        ) {
            config.data = params
            config.params = {}
        }
        config.headers = headers
        return config
    },
    /**
     * 请求拦截器错误处理钩子函数
     * 在请求拦截器发生错误时进行处理。
     * @param err - 错误对象
     * @returns 错误对象
     */
    requestInterceptorsCatchHook(err) {
        NProgress.done() // 结束进度条
        return err
    },
    /**
     * 响应拦截器钩子函数
     * 在接收到响应后进行一些处理，如根据响应码处理不同的逻辑。
     * @param response - Axios响应对象
     * @returns 处理后的响应数据
     */
    async responseInterceptorsHook(response) {
        NProgress.done() // 结束进度条
        const { isTransformResponse, isReturnDefaultResponse } = response.config.requestOptions

        //返回默认响应，当需要获取响应头及其他数据时可使用
        if (isReturnDefaultResponse) {
            return response
        }
        // 是否需要对数据进行处理
        if (!isTransformResponse) {
            return response.data
        }
        const { code, data, show, msg } = response.data
        switch (code) {
            case RequestCodeEnum.SUCCESS:
                if (show) {
                    msg && feedback.msgSuccess(msg) // 显示成功消息
                }
                return data
            case RequestCodeEnum.FAIL:
                if (show) {
                    msg && feedback.msgError(msg) // 显示错误消息
                }
                return Promise.reject(data)
            case RequestCodeEnum.LOGIN_FAILURE:
                clearAuthInfo() // 清空认证信息
                router.push(PageEnum.LOGIN) // 跳转到登录页面
                return Promise.reject()
            case RequestCodeEnum.OPEN_NEW_PAGE:
                window.location.href = data.url // 打开新页面
                return data
            case RequestCodeEnum.NOT_INSTALL:
                window.location.replace('/install/install.php') // 重定向到安装页面
                break
            default:
                return data
        }
    },
    /**
     * 响应拦截器错误处理钩子函数
     * 在响应拦截器发生错误时进行处理。
     * @param error - 错误对象
     * @returns 错误对象
     */
    responseInterceptorsCatchHook(error) {
        NProgress.done() // 结束进度条
        if (error.code !== AxiosError.ERR_CANCELED) {
            error.message && feedback.msgError(error.message) // 显示错误消息
        }
        return Promise.reject(error)
    }
}

/**
 * 默认的Axios配置选项
 */
const defaultOptions: AxiosRequestConfig = {
    //接口超时时间
    timeout: configs.timeout,
    // 基础接口地址
    baseURL: configs.baseUrl,
    //请求头
    headers: { 'Content-Type': ContentTypeEnum.JSON, version: configs.version },
    // 处理 axios的钩子函数
    axiosHooks: axiosHooks,
    // 每个接口可以单独配置
    requestOptions: {
        // 是否将params视为data参数，仅限post请求
        isParamsToData: true,
        //是否返回默认的响应
        isReturnDefaultResponse: false,
        // 需要对返回数据进行处理
        isTransformResponse: true,
        // 接口拼接地址
        urlPrefix: configs.urlPrefix,
        // 忽略重复请求
        ignoreCancelToken: false,
        // 是否携带token
        withToken: true,
        // 开启请求超时重新发起请求请求机制
        isOpenRetry: true,
        // 重新请求次数
        retryCount: 2
    }
}

/**
 * 创建Axios实例
 * @param opt - 部分Axios请求配置
 * @returns Axios实例
 */
function createAxios(opt?: Partial<AxiosRequestConfig>) {
    return new Axios(
        // 深度合并默认配置和传入的配置
        merge(defaultOptions, opt || {})
    )
}
const request = createAxios() // 创建默认的Axios实例
export default request
