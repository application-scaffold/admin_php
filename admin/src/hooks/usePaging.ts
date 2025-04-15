import { reactive, toRaw } from 'vue'

// 分页钩子函数
interface Options {
    page?: number
    size?: number
    fetchFun: (_arg: any) => Promise<any>
    params?: Record<any, any>
    fixedParams?: Record<any, any>
    firstLoading?: boolean
}

/**
 * 自定义钩子用于处理分页逻辑
 * @param options 分页配置选项
 * @returns 返回分页相关的数据和方法
 */
export function usePaging(options: Options) {
    // 解构分页配置选项，设置默认值
    const {
        page = 1,
        size = 15,
        fetchFun,
        params = {},
        fixedParams = {},
        firstLoading = false
    } = options
    // 记录分页初始参数
    const paramsInit: Record<any, any> = Object.assign({}, toRaw(params))
    // 分页数据
    const pager = reactive({
        page,
        size,
        loading: firstLoading,
        count: 0,
        lists: [] as any[],
        extend: {} as Record<string, any>
    })
    /**
     * 请求分页接口
     * @returns 返回请求结果的Promise
     */
    const getLists = () => {
        pager.loading = true
        // 合并分页参数和请求参数，调用fetchFun进行数据请求
        return fetchFun({
            page_no: pager.page,
            page_size: pager.size,
            ...params,
            ...fixedParams
        })
            .then((res: any) => {
                // 更新分页数据
                pager.count = res?.count
                pager.lists = res?.lists
                pager.extend = res?.extend
                return Promise.resolve(res)
            })
            .catch((err: any) => {
                return Promise.reject(err)
            })
            .finally(() => {
                pager.loading = false
            })
    }
    // 重置为第一页
    const resetPage = () => {
        pager.page = 1
        getLists()
    }
    // 重置参数
    const resetParams = () => {
        // 恢复初始参数并请求数据
        Object.keys(paramsInit).forEach((item) => {
            params[item] = paramsInit[item]
        })
        getLists()
    }

    // 返回分页相关的数据和方法
    return {
        pager,
        getLists,
        resetParams,
        resetPage
    }
}
