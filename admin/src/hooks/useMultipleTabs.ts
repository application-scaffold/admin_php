import useTabsStore from '@/stores/modules/multipleTabs'
import useSettingStore from '@/stores/modules/setting'

/**
 * 多标签页管理的自定义钩子函数
 * 该函数用于管理多标签页的功能，包括添加、移除标签页等操作。
 * 它依赖于 Vuex 状态管理中的 tabsStore 和 settingStore，以及 Vue Router 提供的路由功能。
 *
 * @returns 返回一个对象，包含以下属性和方法：
 * - tabsLists: 计算属性，获取当前所有标签页列表
 * - currentTab: 计算属性，获取当前激活的标签页路径
 * - addTab: 方法，用于添加新的标签页
 * - removeTab: 方法，用于移除指定的标签页
 * - removeOtherTab: 方法，用于移除其他所有标签页，仅保留当前标签页
 * - removeAllTab: 方法，用于移除所有标签页
 */
export default function useMultipleTabs() {
    // 获取 Vue Router 实例，用于导航和路由信息
    const router = useRouter()
    const route = useRoute()

    // 获取 Vuex 中的多个模块实例
    const tabsStore = useTabsStore() // 标签页状态管理模块
    const settingStore = useSettingStore() // 设置状态管理模块

    // 计算属性：获取当前所有标签页列表
    const tabsLists = computed(() => {
        return tabsStore.getTabList
    })

    // 计算属性：获取当前激活的标签页路径
    const currentTab = computed(() => {
        return route.fullPath
    })

    /**
     * 添加标签页
     * 如果设置中允许开启多标签页，则调用 tabsStore 的 addTab 方法添加新标签页
     */
    const addTab = () => {
        if (!settingStore.openMultipleTabs) return
        tabsStore.addTab(router)
    }

    /**
     * 移除指定的标签页
     * 如果设置中允许开启多标签页，则调用 tabsStore 的 removeTab 方法移除指定标签页
     * @param fullPath 指定要移除的标签页路径，默认为当前路径
     */
    const removeTab = (fullPath?: any) => {
        if (!settingStore.openMultipleTabs) return
        fullPath = fullPath ?? route.fullPath
        tabsStore.removeTab(fullPath, router)
    }

    /**
     * 移除其他所有标签页
     * 如果设置中允许开启多标签页，则调用 tabsStore 的 removeOtherTab 方法移除其他所有标签页
     */
    const removeOtherTab = () => {
        if (!settingStore.openMultipleTabs) return
        tabsStore.removeOtherTab(route)
    }

    /**
     * 移除所有标签页
     * 如果设置中允许开启多标签页，则调用 tabsStore 的 removeAllTab 方法移除所有标签页
     */
    const removeAllTab = () => {
        if (!settingStore.openMultipleTabs) return
        tabsStore.removeAllTab(router)
    }

    // 返回管理多标签页所需的属性和方法
    return {
        tabsLists,
        currentTab,
        addTab,
        removeTab,
        removeOtherTab,
        removeAllTab
    }
}
