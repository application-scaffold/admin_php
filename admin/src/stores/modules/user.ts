import { defineStore } from 'pinia'
import type { RouteRecordRaw } from 'vue-router'

import { getUserInfo, login, logout } from '@/api/user'
import { TOKEN_KEY } from '@/enums/cacheEnums'
import { PageEnum } from '@/enums/pageEnum'
import router, { filterAsyncRoutes } from '@/router'
import { clearAuthInfo, getToken } from '@/utils/auth'
import cache from '@/utils/cache'

/**
 * 定义用户状态的接口，包含用户的token、信息、路由权限和权限列表。
 */
export interface UserState {
    token: string // 用户的认证令牌
    userInfo: Record<string, any> // 用户的基本信息
    routes: RouteRecordRaw[] // 用户可访问的动态路由列表
    perms: string[] // 用户的权限列表
}

/**
 * 定义一个Pinia Store，用于管理用户相关的状态和操作。
 */
const useUserStore = defineStore({
    id: 'user',
    state: (): UserState => ({
        token: getToken() || '', // 从缓存中获取当前用户的token，如果不存在则为空字符串
        // 用户信息
        userInfo: {}, // 初始化用户信息为空对象
        // 路由
        routes: [], // 初始化用户可访问的路由为空数组
        // 权限
        perms: [] // 初始化用户权限为空数组
    }),
    getters: {},
    actions: {
        /**
         * 重置用户状态。
         * 清空用户的token、信息和权限。
         */
        resetState() {
            this.token = ''
            this.userInfo = {}
            this.perms = []
        },
        /**
         * 用户登录操作。
         * @param playload - 包含账户名和密码的对象
         * @returns 返回一个Promise，resolve时返回登录数据，reject时返回错误信息
         */
        login(playload: any) {
            const { account, password } = playload
            return new Promise((resolve, reject) => {
                login({
                    account: account.trim(), // 去除账户名前后空格
                    password: password
                })
                    .then((data) => {
                        this.token = data.token // 将返回的token保存到状态中
                        cache.set(TOKEN_KEY, data.token) // 将token保存到缓存中
                        resolve(data) // 成功时resolve返回数据
                    })
                    .catch((error) => {
                        reject(error) // 失败时reject返回错误
                    })
            })
        },
        /**
         * 用户登出操作。
         * 清空用户的token，并跳转到登录页面。
         * @returns 返回一个Promise，resolve时返回登出数据，reject时返回错误信息
         */
        logout() {
            return new Promise((resolve, reject) => {
                logout()
                    .then(async (data) => {
                        this.token = '' // 清空用户的token
                        await router.push(PageEnum.LOGIN) // 跳转到登录页面
                        clearAuthInfo() // 清空本地存储的认证信息
                        resolve(data) // 成功时resolve返回数据
                    })
                    .catch((error) => {
                        reject(error) // 失败时reject返回错误
                    })
            })
        },
        /**
         * 获取用户信息。
         * 包括用户的基本信息、权限和可访问的路由。
         * @returns 返回一个Promise，resolve时返回用户信息数据，reject时返回错误信息
         */
        getUserInfo() {
            return new Promise((resolve, reject) => {
                getUserInfo()
                    .then((data) => {
                        this.userInfo = data.user // 保存用户的基本信息
                        this.perms = data.permissions // 保存用户的权限列表
                        this.routes = filterAsyncRoutes(data.menu) // 过滤并保存用户的动态路由
                        resolve(data) // 成功时resolve返回数据
                    })
                    .catch((error) => {
                        reject(error) // 失败时reject返回错误
                    })
            })
        }
    }
})

export default useUserStore
