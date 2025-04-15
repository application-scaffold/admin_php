import useUserStore from '@/stores/modules/user'

/**
 * 检查用户是否拥有指定的所有权限
 *
 * 此函数用于验证用户是否具备给定的权限列表中的所有权限它通过比较用户权限列表（从用户存储中获取）和传入的权限列表来确定
 * 如果用户拥有所有传入的权限或拥有全权限（'*'），则认为验证通过否则，验证失败
 *
 * @param perms {string[]} - 一个包含需要验证的权限的数组
 * @returns {boolean} - 如果用户拥有所有指定的权限，则返回true；否则返回false
 */
export const hasPermission = (perms: string[]): boolean => {
    // 使用用户存储来获取当前用户的权限列表
    const userStore = useUserStore()
    const permissions = userStore.perms
    // 定义全权限标识，拥有此权限的用户可以执行任何操作
    const all_permission = '*'

    // 如果传入的权限列表长度大于0，则进行权限验证
    if (perms.length > 0) {
        // 默认认为用户拥有所有传入的权限
        let hasPermission = true
        // 遍历传入的权限列表，检查用户是否拥有每个权限
        perms.forEach((key: string) => {
            // 如果用户没有当前权限且没有全权限，则标记为没有所有权限
            if (!permissions.includes(key) && !permissions.includes(all_permission)) {
                hasPermission = false
            }
        })
        // 返回用户是否拥有所有传入权限的结果
        return hasPermission
    } else {
        // 如果传入的权限列表为空，则认为用户自动拥有所有权限
        return true
    }
}
