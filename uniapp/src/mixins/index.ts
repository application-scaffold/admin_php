import { App } from 'vue'
import theme from './theme'
export function setupMixin(app: App) {
    // 将 theme 中定义的选项（如 data、methods、生命周期钩子 等）混入到所有 Vue 组件
    app.mixin(theme)
}
