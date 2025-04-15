import colors from 'css-color-function'

// 定义浅色主题的配置，使用css-color-function的tint函数来生成不同的色调
const lightConfig = {
    'dark-2': 'shade(20%)',
    'light-3': 'tint(30%)',
    'light-5': 'tint(50%)',
    'light-7': 'tint(70%)',
    'light-8': 'tint(80%)',
    'light-9': 'tint(90%)'
}

// 定义深色主题的配置，使用css-color-function的shade函数来生成不同的色调
const darkConfig = {
    'light-3': 'shade(20%)',
    'light-5': 'shade(30%)',
    'light-7': 'shade(50%)',
    'light-8': 'shade(60%)',
    'light-9': 'shade(70%)',
    'dark-2': 'tint(20%)'
}

// 定义CSS变量的根元素ID
const themeId = 'theme-vars'

/**
 * 生成Element UI主题的行为变量
 *
 * 根据传入的颜色、类型和主题模式生成相应的CSS变量
 *
 * @param color {string} - 主题颜色，例如'#409EFF'
 * @param type {string} - 主题类型，默认为'primary'，可选值有'primary', 'success', 'warning', 'danger', 'error', 'info'
 * @param isDark {boolean} - 是否为深色主题，默认为false
 * @returns {Record<string, string>} - 包含生成的CSS变量的对象
 */
export const generateVars = (color: string, type = 'primary', isDark = false) => {
    const colos = {
        [`--el-color-${type}`]: color
    }
    const config: Record<string, string> = isDark ? darkConfig : lightConfig
    for (const key in config) {
        colos[`--el-color-${type}-${key}`] = `color(${color} ${config[key]})`
    }
    return colos
}

/**
 * 设置CSS变量
 *
 * 根据传入的key和value设置指定DOM元素的CSS变量
 *
 * @param key {string} - CSS变量的key，例如'--color-primary'
 * @param value {string} - CSS变量的值，例如'#f40'
 * @param dom {HTMLElement} - 要设置CSS变量的DOM元素，默认为document.documentElement
 */
export const setCssVar = (key: string, value: string, dom = document.documentElement) => {
    dom.style.setProperty(key, value)
}

/**
 * 设置主题
 *
 * 根据传入的主题选项和主题模式设置全局CSS变量
 *
 * @param options {Record<string, string>} - 主题选项，键为类型（如'primary'），值为颜色（如'#409EFF'）
 * @param isDark {boolean} - 是否为深色主题，默认为false
 */
export const setTheme = (options: Record<string, string>, isDark = false) => {
    const varsMap: Record<string, string> = Object.keys(options).reduce((prev, key) => {
        return Object.assign(prev, generateVars(options[key], key, isDark))
    }, {})

    // 将生成的CSS变量转换为CSS字符串
    let theme = Object.keys(varsMap).reduce((prev, key) => {
        const color = colors.convert(varsMap[key])
        return `${prev}${key}:${color};`
    }, '')
    theme = `:root{${theme}}`

    // 创建或更新<style>标签以应用主题
    let style = document.getElementById(themeId)
    if (style) {
        style.innerHTML = theme
        return
    }
    style = document.createElement('style')
    style.setAttribute('type', 'text/css')
    style.setAttribute('id', themeId)
    style.innerHTML = theme
    document.head.append(style)
}
