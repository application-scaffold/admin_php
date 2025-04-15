/**
 * 判断给定的路径是否是外部路径
 *
 * 该函数通过正则表达式检查路径是否以http、https、mailto或tel协议开头
 * 这些协议通常表示路径将引导用户离开当前应用程序
 *
 * @param {string} path 要检查的路径
 * @returns {Boolean} 如果路径是外部路径则返回true，否则返回false
 */
export function isExternal(path: string) {
    return /^(https?:|mailto:|tel:)/.test(path)
}
