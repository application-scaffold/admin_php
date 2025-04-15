import { isObject } from '@vue/shared'
import { cloneDeep } from 'lodash'

/**
 * 为数值添加单位
 *
 * @param value {string | number} - 要添加单位的值，例如100
 * @param unit {string} - 单位，默认为'px'，可选值有'px', 'em', 'rem'等
 * @returns {string} - 添加单位后的字符串
 */
export const addUnit = (value: string | number, unit = 'px') => {
    return !Object.is(Number(value), NaN) ? `${value}${unit}` : value
}

/**
 * 检查值是否为空
 *
 * @param value {unknown} - 要检查的值
 * @returns {boolean} - 如果值为null或undefined，则返回true；否则返回false
 */
export const isEmpty = (value: unknown) => {
    return value == null && typeof value == 'undefined'
}

/**
 * 将树结构数据转换为数组，使用队列实现广度优先遍历
 *
 * @param data {any[]} - 树结构数据
 * @param props {Object} - 配置对象，默认为`{ children: 'children' }`
 * @returns {any[]} - 转换后的数组
 */
export const treeToArray = (data: any[], props = { children: 'children' }) => {
    data = cloneDeep(data)
    const { children } = props
    const newData = []
    const queue: any[] = []
    data.forEach((child: any) => queue.push(child))
    while (queue.length) {
        const item: any = queue.shift()
        if (item[children]) {
            item[children].forEach((child: any) => queue.push(child))
            delete item[children]
        }
        newData.push(item)
    }
    return newData
}

/**
 * 将数组转换为树结构数据
 *
 * @param data {any[]} - 数组数据
 * @param props {Object} - 配置对象，默认为`{ id: 'id', parentId: 'pid', children: 'children' }`
 * @returns {any[]} - 转换后的树结构数据
 */
export const arrayToTree = (
    data: any[],
    props = { id: 'id', parentId: 'pid', children: 'children' }
) => {
    data = cloneDeep(data)
    const { id, parentId, children } = props
    const result: any[] = []
    const map = new Map()
    data.forEach((item) => {
        map.set(item[id], item)
        const parent = map.get(item[parentId])
        if (parent) {
            parent[children] = parent[children] ?? []
            parent[children].push(item)
        } else {
            result.push(item)
        }
    })
    return result
}

/**
 * 获取标准化的路径
 *
 * @param path {string} - 路径字符串
 * @returns {string} - 标准化后的路径
 */
export function getNormalPath(path: string) {
    if (path.length === 0 || !path || path == 'undefined') {
        return path
    }
    const newPath = path.replace('//', '/')
    const length = newPath.length
    if (newPath[length - 1] === '/') {
        return newPath.slice(0, length - 1)
    }
    return newPath
}

/**
 * 将对象格式化为Query语法字符串
 *
 * @param params {Record<string, any>} - 对象参数
 * @returns {string} - Query语法字符串
 */
export function objectToQuery(params: Record<string, any>): string {
    let query = ''
    for (const props of Object.keys(params)) {
        const value = params[props]
        const part = encodeURIComponent(props) + '='
        if (!isEmpty(value)) {
            if (isObject(value)) {
                for (const key of Object.keys(value)) {
                    if (!isEmpty(value[key])) {
                        const params = props + '[' + key + ']'
                        const subPart = encodeURIComponent(params) + '='
                        query += subPart + encodeURIComponent(value[key]) + '&'
                    }
                }
            } else {
                query += part + encodeURIComponent(value) + '&'
            }
        }
    }
    return query.slice(0, -1)
}

/**
 * 格式化时间
 *
 * @param dateTime {number} - 时间戳，单位为毫秒
 * @param fmt {string} - 时间格式，默认为'yyyy-mm-dd'
 * @returns {string} - 格式化后的时间字符串
 */
// yyyy:mm:dd|yyyy:mm|yyyy年mm月dd日|yyyy年mm月dd日 hh时MM分等,可自定义组合
export const timeFormat = (dateTime: number, fmt = 'yyyy-mm-dd') => {
    // 如果为null,则格式化当前时间
    if (!dateTime) {
        dateTime = Number(new Date())
    }
    // 如果dateTime长度为10或者13，则为秒和毫秒的时间戳，如果超过13位，则为其他的时间格式
    if (dateTime.toString().length == 10) {
        dateTime *= 1000
    }
    const date = new Date(dateTime)
    let ret
    const opt: any = {
        'y+': date.getFullYear().toString(), // 年
        'm+': (date.getMonth() + 1).toString(), // 月
        'd+': date.getDate().toString(), // 日
        'h+': date.getHours().toString(), // 时
        'M+': date.getMinutes().toString(), // 分
        's+': date.getSeconds().toString() // 秒
    }
    for (const k in opt) {
        ret = new RegExp('(' + k + ')').exec(fmt)
        if (ret) {
            fmt = fmt.replace(
                ret[1],
                ret[1].length == 1 ? opt[k] : opt[k].padStart(ret[1].length, '0')
            )
        }
    }
    return fmt
}

/**
 * 生成不重复的ID
 *
 * @param length {number} - ID的长度，默认为8
 * @returns {string} - 生成的ID
 */
export const getNonDuplicateID = (length = 8) => {
    let idStr = Date.now().toString(36)
    idStr += Math.random().toString(36).substring(3, length)
    return idStr
}

/**
 * 计算颜色的透明度
 *
 * @param color {string} - 颜色值，支持hex、rgb和rgba格式
 * @param opacity {number} - 透明度值，范围为0到1
 * @returns {string} - 计算后的rgba颜色值
 */
export const calcColor = (color: string, opacity: number): string => {
    // 规范化透明度值在 0 ~ 1 之间
    opacity = Math.min(1, Math.max(0, opacity))

    // 检查颜色是否是 hex 格式
    const isHex = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/
    const isRgb = /^rgb\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*\)$/
    const isRgba = /^rgba\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*,\s*[0-9.]+\s*\)$/

    let r: number = 0,
        g: number = 0,
        b: number = 0

    if (isHex.test(color)) {
        // 如果是 hex 格式 (#ffffff 或 #fff)
        const hex = color.slice(1)

        // 如果是3位短格式，扩展为6位
        const fullHex =
            hex.length === 3
                ? hex
                      .split('')
                      .map((h) => h + h)
                      .join('')
                : hex

        // 转换为 RGB
        r = parseInt(fullHex.substring(0, 2), 16)
        g = parseInt(fullHex.substring(2, 4), 16)
        b = parseInt(fullHex.substring(4, 6), 16)
    } else if (isRgb.test(color)) {
        // 如果是 rgb 格式 (rgb(255, 255, 255))
        const rgbValues = color.match(/\d+/g)
        if (rgbValues) {
            r = parseInt(rgbValues[0])
            g = parseInt(rgbValues[1])
            b = parseInt(rgbValues[2])
        }
    } else if (isRgba.test(color)) {
        // 如果是 rgba 格式 (rgba(255, 255, 255, 1))
        const rgbaValues = color.match(/\d+(\.\d+)?/g)
        if (rgbaValues) {
            r = parseInt(rgbaValues[0])
            g = parseInt(rgbaValues[1])
            b = parseInt(rgbaValues[2])
        }
    } else {
        throw new Error('Unsupported color format')
    }

    // 返回转换后的 rgba 颜色值
    return `rgba(${r}, ${g}, ${b}, ${opacity})`
}
