import { defineConfig } from 'vite'
import uni from '@dcloudio/vite-plugin-uni'
import tailwindcss from 'tailwindcss'
import autoprefixer from 'autoprefixer'
import postcssRemToResponsivePixel from 'postcss-rem-to-responsive-pixel'
import postcssWeappTailwindcssRename from 'weapp-tailwindcss-webpack-plugin/postcss'
import vwt from 'weapp-tailwindcss-webpack-plugin/vite'
import uniRouter from 'unplugin-uni-router/vite'

const isH5 = process.env.UNI_PLATFORM === 'h5'
const isApp = process.env.UNI_PLATFORM === 'app'
const weappTailwindcssDisabled = isH5 || isApp

// PostCSS 插件链
const postcssPlugin = [
    autoprefixer(),
    tailwindcss() //通过 tailwindcss() 插件启用原子化 CSS，需配合 tailwind.config.js 配置文件
]
// H5/APP 端禁用：避免不必要的转换开销
if (!weappTailwindcssDisabled) {
    postcssPlugin.push(
        postcssRemToResponsivePixel({ // REM 转 RXP 单位
            rootValue: 32, // 设计稿基准值（1rem=32rpx）
            propList: ['*'], // 转换所有属性
            transformUnit: 'rpx'
        })
    )
    postcssPlugin.push(postcssWeappTailwindcssRename()) // 类名重命名
}

// https://vitejs.dev/config/
export default defineConfig({
    // uniRouter 插件会自动生成路由配置，无需手动编写
    plugins: [
        uni(), // uni-app 核心插件，uni-app 官方插件，支持编译到微信小程序/H5/APP 等多端，处理平台差异和组件库转换
        uniRouter(), // 自动生成 uni-app 路由
        weappTailwindcssDisabled ? undefined : vwt() // 小程序 Tailwind 适配插件，解决微信小程序不支持 : 和 @ 符号的问题，自动转换 Tailwind 类名为合法格式
    ],
    css: {
        postcss: {
            // CSS 处理流程
            // 处理顺序为：
            // 1. Tailwind 生成样式
            // 2. Autoprefixer 加前缀
            // 3. REM → RXP 转换（仅小程序）
            // 4. 类名重命名（仅小程序）
            plugins: postcssPlugin
        }
    },
    server: {
        port: 8991
        //支持热更新和代理配置
    }
})
