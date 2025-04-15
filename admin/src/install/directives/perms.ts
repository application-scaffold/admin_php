/**
 * perm 操作权限处理
 * 指令用法：
 *  <el-button v-perms="['auth.menu/edit']">编辑</el-button>
 */

import useUserStore from '@/stores/modules/user'

/**
 * 自定义指令用于权限控制，根据用户权限动态显示或隐藏元素。
 */
export default {
    /**
     * 在指令绑定到元素时调用，执行权限校验逻辑。
     * @param el - 绑定指令的 DOM 元素
     * @param binding - 指令的绑定信息，包含传递的值等
     */
    mounted: (el: HTMLElement, binding: any) => {
        // 获取指令绑定的值（即权限列表）
        const { value } = binding
        // 获取用户权限存储模块实例
        const userStore = useUserStore()
        // 获取当前用户的权限列表
        const permissions = userStore.perms
        // 定义通配符权限标识，表示拥有所有权限
        const all_permission = '*'
        // 判断绑定的值是否为数组
        if (Array.isArray(value)) {
            // 确保权限列表不为空
            if (value.length > 0) {
                // 检查用户是否拥有任意一个所需的权限
                const hasPermission = permissions.some((key: string) => {
                    return all_permission == key || value.includes(key)
                })

                // 如果用户没有所需权限，则移除该元素
                if (!hasPermission) {
                    el.parentNode && el.parentNode.removeChild(el)
                }
            }
        } else {
            // 如果绑定的值不是数组，抛出错误提示正确的使用方式
            throw new Error('like v-perms="[\'auth.menu/edit\']"')
        }
    }
}
