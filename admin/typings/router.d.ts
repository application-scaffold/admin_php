import 'vue-router'

/**
 * 扩展 Vue Router 的 RouteMeta 接口
 * 添加自定义的元字段，用于路由配置
 */
declare module 'vue-router' {
    // 扩展 RouteMeta
    interface RouteMeta {
        /**
         * 路由的类型
         */
        type?: string
        /**
         * 路由所需的权限
         */
        perms?: string
        /**
         * 路由的标题，通常用于显示在导航栏或面包屑中
         */
        title?: string
        /**
         * 路由的图标，通常用于导航栏
         */
        icon?: string
        /**
         * 是否隐藏该路由，不显示在侧边导航栏中
         */
        hidden?: boolean
        /**
         * 激活菜单时高亮显示侧边栏导航栏的菜单项
         */
        activeMenu?: string
        /**
         * 是否在多标签Tab栏隐藏标签页
         */
        hideTab?: boolean
        /**
         * 是否使用 keep-alive 缓存该路由组件
         */
        keepAlive?: boolean
    }
}
