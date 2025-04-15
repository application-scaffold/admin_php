import { isObject } from '@vue/shared'
import { defineStore } from 'pinia'

import defaultSetting from '@/config/setting'
import { SETTING_KEY } from '@/enums/cacheEnums'
import cache from '@/utils/cache'
import { setTheme } from '@/utils/theme'

const storageSetting = cache.get(SETTING_KEY)

export const useSettingStore = defineStore({
    id: 'setting', // Store的唯一标识符
    state: () => {
        const state = {
            showDrawer: false, // 是否显示设置抽屉
            ...defaultSetting // 默认设置对象
        }
        // 如果存在存储的设置，则将其合并到初始状态中
        isObject(storageSetting) && Object.assign(state, storageSetting)
        return state
    },
    actions: {
        /**
         * 设置布局配置。
         * @param data - 包含key和value的对象，用于更新特定的设置项
         */
        setSetting(data: Record<string, any>) {
            const { key, value } = data
            if (this.hasOwnProperty(key)) {
                //@ts-ignore
                this[key] = value // 更新指定的设置项
            }
            const settings: any = Object.assign({}, this.$state) // 深拷贝当前状态
            delete settings.showDrawer // 移除showDrawer字段，避免保存到缓存中
            cache.set(SETTING_KEY, settings) // 将更新后的设置保存到缓存中
        },
        /**
         * 设置主题颜色。
         * @param isDark - 是否为暗黑模式
         */
        setTheme(isDark: boolean) {
            setTheme(
                {
                    primary: this.theme, // 主题色
                    success: this.successTheme, // 成功色
                    warning: this.warningTheme, // 警告色
                    danger: this.dangerTheme, // 危险色
                    error: this.errorTheme, // 错误色
                    info: this.infoTheme // 信息色
                },
                isDark // 是否为暗黑模式
            )
        },
        /**
         * 重置主题设置为默认值。
         */
        resetTheme() {
            for (const key in defaultSetting) {
                //@ts-ignore
                this[key] = defaultSetting[key] // 将所有设置项重置为默认值
            }
            cache.remove(SETTING_KEY) // 从缓存中移除设置数据
        }
    }
})

export default useSettingStore
