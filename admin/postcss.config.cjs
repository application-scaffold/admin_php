// Vite 5+：无需在 vite.config.ts 中重复声明 PostCSS 插件，但必须保留 postcss.config.js
module.exports = {
    // 配置PostCSS插件
    plugins: {
        // 使用Tailwind CSS插件
        tailwindcss: {},
        // 使用Autoprefixer插件以自动添加浏览器前缀
        autoprefixer: {}
    }
}
