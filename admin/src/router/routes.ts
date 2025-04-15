/**
 * Note: 路由配置项
 *
 * path: '/path'                    // 路由路径
 * name:'router-name'               // 设定路由的名字，一定要填写不然使用<keep-alive>时会出现各种问题
 * meta : {
	title: 'title'                  // 设置该路由在侧边栏的名字
	icon: 'icon-name'                // 设置该路由的图标
	activeMenu: '/system/user'      // 当路由设置了该属性，则会高亮相对应的侧边栏。
	query: '{"id": 1}'             // 访问路由的默认传递参数
	hidden: true                   // 当设置 true 的时候该路由不会在侧边栏出现 
    hideTab: true                   //当设置 true 的时候该路由不会在多标签tab栏出现
  }
 */

import type { RouteRecordRaw } from 'vue-router'

import { PageEnum } from '@/enums/pageEnum'
import Layout from '@/layout/default/index.vue'

/**
 * 异步获取Layout组件
 *
 * 该函数返回一个Promise，该Promise在解析时会返回Layout组件
 * 使用这种方式可以实现按需加载，提高应用的性能和用户体验
 *
 * @returns {Promise<typeof Layout>} 一个解析为Layout组件的Promise
 */
export const LAYOUT = () => Promise.resolve(Layout)

// 定义一个唯一的路由名称标识符，用于区分不同的路由
export const INDEX_ROUTE_NAME = Symbol()

/**
 * 定义应用的常量路由配置，这些路由在应用初始化时会被加载。
 * 包含了404页面、403页面、登录页面等基础路由，以及部分用户和个人设置相关的路由。
 */
export const constantRoutes: Array<RouteRecordRaw> = [
    /**
     * 捕获所有未匹配的路径，重定向到404页面。
     */
    {
        path: '/:pathMatch(.*)*',
        component: () => import('@/views/error/404.vue')
    },
    /**
     * 权限不足时跳转的403错误页面。
     */
    {
        path: PageEnum.ERROR_403,
        component: () => import('@/views/error/403.vue')
    },
    /**
     * 用户登录页面路由。
     */
    {
        path: PageEnum.LOGIN,
        component: () => import('@/views/account/login.vue')
    },
    /**
     * 用户相关路由，包含个人设置子路由。
     */
    {
        path: '/user',
        component: LAYOUT, // 使用默认布局组件
        children: [
            {
                path: 'setting',
                component: () => import('@/views/user/setting.vue'),
                name: Symbol(), // 唯一标识符，避免重复命名冲突
                meta: {
                    title: '个人设置' // 路由标题
                }
            }
        ]
    },
    /**
     * 装饰详情页面路由，直接映射到具体组件。
     */
    {
        path: '/decoration/pc_details',
        component: () => import('@/views/decoration/pc_details.vue')
    }
    // {
    //     path: '/dev_tools',
    //     component: LAYOUT,
    //     children: [
    //         {
    //             path: 'code/edit',
    //             component: () => import('@/views/dev_tools/code/edit.vue'),
    //             meta: {
    //                 title: '编辑数据表',
    //                 activeMenu: '/dev_tools/code'
    //             }
    //         }
    //     ]
    // },
    // {
    //     path: '/setting',
    //     component: LAYOUT,
    //     children: [
    //         {
    //             path: 'dict/data',
    //             component: () => import('@/views/setting/dict/data/index.vue'),
    //             meta: {
    //                 title: '数据管理',
    //                 activeMenu: '/setting/dict'
    //             }
    //         }
    //     ]
    // }
]

/**
 * 定义首页路由配置。
 * 包含首页路径、使用的布局组件以及唯一标识符。
 */
export const INDEX_ROUTE: RouteRecordRaw = {
    path: PageEnum.INDEX,
    component: LAYOUT,
    name: INDEX_ROUTE_NAME
}
