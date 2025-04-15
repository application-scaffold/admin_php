/**
 * perm 操作权限处理
 * 指令用法：
 *  <el-button v-perms="['auth.menu/edit']">编辑</el-button>
 */

import useClipboard from 'vue-clipboard3'

import feedback from '@/utils/feedback'

/**
 * 定义一个常量，用于存储 HTML 元素的自定义属性名称，该属性将保存需要复制的文本内容。
 */
const clipboard = 'data-clipboard-text'
/**
 * 自定义指令的逻辑实现，包含 mounted 和 updated 两个生命周期钩子。
 */
export default {
    /**
     * 指令绑定到元素时调用，初始化复制功能。
     * @param el - 绑定指令的 DOM 元素
     * @param binding - 指令的绑定信息，包含传递的值等
     */
    mounted: (el: HTMLElement, binding: any) => {
        // 为元素设置自定义属性，存储需要复制的文本内容
        el.setAttribute(clipboard, binding.value)
        // 引入剪贴板功能
        const { toClipboard } = useClipboard()

        // 为元素绑定点击事件，实现复制功能
        el.onclick = () => {
            toClipboard(el.getAttribute(clipboard)!) // 复制指定的文本内容
                .then(() => {
                    feedback.msgSuccess('复制成功') // 复制成功时的提示
                })
                .catch(() => {
                    feedback.msgError('复制失败') // 复制失败时的提示
                })
        }
    },
    /**
     * 指令所在的模板更新时调用，更新复制内容。
     * @param el - 绑定指令的 DOM 元素
     * @param binding - 指令的绑定信息，包含传递的值等
     */
    updated: (el: HTMLElement, binding: any) => {
        // 更新元素的自定义属性，确保复制内容是最新的
        el.setAttribute(clipboard, binding.value)
    }
}
