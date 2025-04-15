import type { App } from 'vue'

// 路径匹配 ./**/* 表示递归匹配当前目录及其所有子目录下的所有文件（含嵌套文件）
// { eager: true } 表示禁用动态导入（Lazy Import），直接同步加载所有匹配的模块
const modules = import.meta.glob('./**/*', { eager: true })

/**
 * 注册模块到应用程序中
 * 该函数遍历模块对象，根据模块的类型进行不同的处理，以注册或使用这些模块
 * @param {App<Element>} app - 应用程序实例，用于注册指令或使用插件
 */
function install(app: App<Element>) {
    // 遍历模块对象，提取模块的键和值
    Object.keys(modules).forEach((key) => {
        // 提取模块名称，用于注册时的标识
        const name = key.replace(/(.*\/)*([^.]+).*/gi, '$2')
        // 提取模块类型，决定如何处理模块（指令或插件）
        const type = key.replace(/^\.\/([\w-]+).*/gi, '$1')
        // 动态获取模块对象
        const module: any = modules[key]
        // 检查模块是否有默认导出，如果有，则根据其类型进行处理
        if (module.default) {
            switch (type) {
                // 用于注册全局指令
                case 'directives':
                    // 注册全局指令，指令名称为提取的模块名称，指令本身为模块的默认导出
                    app.directive(name, module.default)
                    break
                // 使用插件
                case 'plugins':
                    // 如果插件的默认导出是一个函数，则调用该函数，并传入应用程序实例
                    typeof module.default === 'function' && module.default(app)
                    break
            }
        }
    })
}

export default {
    install
}
