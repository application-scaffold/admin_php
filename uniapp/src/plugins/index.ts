import { isFunction } from '@vue/shared'
import { App } from 'vue'
const modules = import.meta.globEager('./modules/**/*.ts')

export default {
    // install 方法是 Vue 插件的标准入口，接收 app 实例作为参数
    install: (app: App) => {
        for (const module of Object.values(modules)) {
            const fun = module.default
            isFunction(fun) && fun(app)
        }
    }
}
