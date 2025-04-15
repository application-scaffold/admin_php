import vue from '@vitejs/plugin-vue'
import vueJsx from '@vitejs/plugin-vue-jsx'
// 自动生成 .eslintrc-auto-import.json 配置文件
import AutoImport from 'unplugin-auto-import/vite'
import { ElementPlusResolver } from 'unplugin-vue-components/resolvers'
// 自动生成 auto-import.d.ts 文件
import Components from 'unplugin-vue-components/vite'
import { fileURLToPath, URL } from 'url'
import { defineConfig } from 'vite'
import { createStyleImportPlugin, ElementPlusResolve } from 'vite-plugin-style-import'
import { createSvgIconsPlugin } from 'vite-plugin-svg-icons'
import vueSetupExtend from 'vite-plugin-vue-setup-extend'
// import legacyPlugin from '@vitejs/plugin-legacy'
// https://vitejs.dev/config/
export default defineConfig({
    // 设置应用的基础路径
    base: '/admin/',
    // 开发服务器配置
    server: {
        host: '0.0.0.0'
    },
    // 插件配置
    plugins: [
        // 使用Vue插件
        vue(),
        // 使用Vue JSX插件
        vueJsx(),
        // 自动导入Vue和Vue Router的API，并生成ESLint配置
        AutoImport({
            imports: ['vue', 'vue-router'],
            resolvers: [ElementPlusResolver()],
            eslintrc: {
                enabled: true
            }
        }),
        // 自动注册 components 文件夹中 Vue 组件，并使用 Element Plus 解析器
        Components({
            directoryAsNamespace: true,
            resolvers: [ElementPlusResolver()]
        }),
        // 按需导入Element Plus的样式
        createStyleImportPlugin({
            resolves: [ElementPlusResolve()]
        }),
        // SVG图标插件配置
        createSvgIconsPlugin({
            // 配置SVG图标存放路径
            iconDirs: [fileURLToPath(new URL('./src/assets/icons', import.meta.url))],
            // 生成的SVG图标ID格式
            symbolId: 'local-icon-[dir]-[name]'
        }),
        // 扩展Vue Setup语法
        // 通过此插件，开发者可以直接在 <script setup> 标签中定义组件名（name 属性），避免了传统方式需要额外编写 <script> 标签的繁琐
        vueSetupExtend()
        // legacyPlugin({
        //     targets: ['defaults', 'IE 11']
        // })
    ],
    // 模块解析配置
    resolve: {
        // 路径别名配置
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url))
        }
    },
    // 构建配置
    build: {
        chunkSizeWarningLimit: 1024, // 单位 KB
        rollupOptions: {
            output: {
                // 手动分包配置，将 node_modules 中的每个第三方包拆分为独立 chunk
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return id.toString().split('node_modules/')[1].split('/')[0].toString()
                    }
                }
            }
        }
    },
    // 定义全局常量
    define: {
        __VUE_PROD_DEVTOOLS__: false,
        __VUE_OPTIONS_API__: true,
        __VUE_PROD_HYDRATION__: true,
        __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: false
    }
})
