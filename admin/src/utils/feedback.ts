import {
    ElLoading,
    ElMessage,
    ElMessageBox,
    type ElMessageBoxOptions,
    ElNotification
} from 'element-plus'
import type { LoadingInstance } from 'element-plus/es/components/loading/src/loading'

/**
 * 反馈类，用于处理各种用户反馈信息，如消息提示、弹窗、通知等。
 */
export class Feedback {
    private loadingInstance: LoadingInstance | null = null
    static instance: Feedback | null = null

    /**
     * 获取 Feedback 类的单例实例
     *
     * @returns {Feedback} Feedback 类的单例实例
     */
    static getInstance() {
        return this.instance ?? (this.instance = new Feedback())
    }
    /**
     * 显示信息消息提示
     *
     * @param {string} msg - 消息内容
     */
    msg(msg: string) {
        ElMessage.info(msg)
    }

    /**
     * 显示错误消息提示
     *
     * @param {string} msg - 消息内容
     */
    msgError(msg: string) {
        ElMessage.error(msg)
    }
    /**
     * 显示成功消息提示
     *
     * @param {string} msg - 消息内容
     */
    msgSuccess(msg: string) {
        ElMessage.success(msg)
    }
    /**
     * 显示警告消息提示
     *
     * @param {string} msg - 消息内容
     */
    msgWarning(msg: string) {
        ElMessage.warning(msg)
    }
    /**
     * 显示信息弹窗
     *
     * @param {string} msg - 消息内容
     */
    alert(msg: string) {
        ElMessageBox.alert(msg, '系统提示')
    }
    /**
     * 显示错误弹窗
     *
     * @param {string} msg - 消息内容
     */
    alertError(msg: string) {
        ElMessageBox.alert(msg, '系统提示', { type: 'error' })
    }
    /**
     * 显示成功弹窗
     *
     * @param {string} msg - 消息内容
     */
    alertSuccess(msg: string) {
        ElMessageBox.alert(msg, '系统提示', { type: 'success' })
    }
    /**
     * 显示警告弹窗
     *
     * @param {string} msg - 消息内容
     */
    alertWarning(msg: string) {
        ElMessageBox.alert(msg, '系统提示', { type: 'warning' })
    }
    /**
     * 显示信息通知
     *
     * @param {string} msg - 消息内容
     */
    notify(msg: string) {
        ElNotification.info(msg)
    }
    /**
     * 显示错误通知
     *
     * @param {string} msg - 消息内容
     */
    notifyError(msg: string) {
        ElNotification.error(msg)
    }
    /**
     * 显示成功通知
     *
     * @param {string} msg - 消息内容
     */
    notifySuccess(msg: string) {
        ElNotification.success(msg)
    }
    /**
     * 显示警告通知
     *
     * @param {string} msg - 消息内容
     */
    notifyWarning(msg: string) {
        ElNotification.warning(msg)
    }
    /**
     * 显示确认对话框
     *
     * @param {string} msg - 消息内容
     * @returns {Promise} Promise 对象，解析为用户点击的按钮类型
     */
    confirm(msg: string) {
        return ElMessageBox.confirm(msg, '温馨提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
        })
    }
    /**
     * 显示输入对话框
     *
     * @param {string} content - 输入框内容
     * @param {string} title - 对话框标题
     * @param {ElMessageBoxOptions} [options] - 可选的对话框选项
     * @returns {Promise} Promise 对象，解析为用户输入的值
     */
    prompt(content: string, title: string, options?: ElMessageBoxOptions) {
        return ElMessageBox.prompt(content, title, {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            ...options
        })
    }
    /**
     * 打开全局加载提示
     *
     * @param {string} msg - 加载提示信息
     */
    loading(msg: string) {
        this.loadingInstance = ElLoading.service({
            lock: true,
            text: msg
        })
    }
    /**
     * 关闭全局加载提示
     */
    closeLoading() {
        this.loadingInstance?.close()
    }
}

const feedback = Feedback.getInstance()

export default feedback
