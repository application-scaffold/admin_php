import { reactive, toRaw } from 'vue'

import { getDictData } from '@/api/app'

interface Options {
    [propName: string]: {
        api: PromiseFun
        params?: Record<string, any>
        transformData?(data: any): any
    }
}

// {
//     dict: {
//         api: dictData,
//         params: { name: 'user' },
//         transformData(data: any) {
//             return data.list
//         }
//     }
// }

/**
 * 自定义钩子用于获取字典选项数据
 * 该钩子的主要作用是根据提供的配置，异步获取字典数据，并提供一个刷新数据的方法
 *
 * @param options 包含字典数据获取配置的对象，每个配置包括API调用和参数
 * @returns 返回一个对象，包含异步获取的字典数据和一个用于刷新数据的函数
 */
export function useDictOptions<T = any>(options: Options) {
    // 创建一个响应式的对象来存储各个字典选项的数据
    const optionsData: any = reactive({})
    // 获取配置项的键名列表
    const optionsKey = Object.keys(options)
    // 根据配置项，生成API调用列表
    const apiLists = optionsKey.map((key) => {
        const value = options[key]
        // 初始化每个配置项对应的数据列表为空数组
        optionsData[key] = []
        return () => value.api(toRaw(value.params) || {})
    })

    /**
     * 刷新字典数据的方法
     * 该方法会异步调用所有配置项中的API，并更新字典数据
     */
    const refresh = async () => {
        // 并发调用所有API，并获取结果
        const res = await Promise.allSettled<Promise<any>>(apiLists.map((api) => api()))
        // 遍历所有API调用的结果，并更新数据
        res.forEach((item, index) => {
            const key = optionsKey[index]
            if (item.status == 'fulfilled') {
                // 获取当前配置项的数据显示转换函数
                const { transformData } = options[key]
                // 根据转换函数处理API返回的数据，如果没有提供转换函数，则直接使用原始数据
                const data = transformData ? transformData(item.value) : item.value
                // 更新响应式数据
                optionsData[key] = data
            }
        })
    }
    // 初始调用刷新方法，以获取数据
    refresh()

    // 返回字典数据和刷新方法，字典数据类型根据泛型参数T确定
    return {
        optionsData: optionsData as T,
        refresh
    }
}

// useDictData<{
//     dict: any[]
// }>(['dict'])

/**
 * 使用字典数据的自定义钩子
 * 该钩子用于从服务器获取字典数据并提供刷新机制
 *
 * @param dict 字典类型字符串，用于查询特定的字典数据
 * @returns 返回一个对象，包含字典数据和刷新字典数据的方法
 */
export function useDictData<T = any>(dict: string) {
    // 创建一个响应式的字典数据对象，用于存储从服务器获取的数据
    const dictData: any = reactive({})

    /**
     * 刷新字典数据的方法
     * 该方法会异步请求服务器，获取最新的字典数据，并更新dictData
     */
    const refresh = async () => {
        // 向服务器请求特定类型的字典数据
        const data = await getDictData({
            type: dict
        })
        // 将获取到的数据合并到dictData中，以响应式地更新数据
        Object.assign(dictData, data)
    }
    // 初始调用refresh方法，以确保在钩子首次使用时加载数据
    refresh()

    // 返回字典数据和刷新方法，允许组件访问和刷新数据
    return {
        dictData: dictData as T,
        refresh
    }
}
