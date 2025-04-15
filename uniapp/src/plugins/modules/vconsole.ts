// url中携带参数vconsole == vconsoleMd5时打开Vconsole，可以用于手机浏览器中调试
// 如/mobile/pages/user/user?vconsole=47b1e3a9d33e6064e58cc4796c708447，此时在个人中心页面打开vconsole

// 这段代码用于在 H5 环境中动态启用 vConsole 调试工具，通过 URL 参数控制其加载
const vconsoleMd5 = '47b1e3a9d33e6064e58cc4796c708447'
export default async () => {
    // #ifdef H5
    // 通过 #ifdef H5 条件编译，仅作用于 Web 端
    const url = new URL(location.href)
    const searchParams = new URLSearchParams(url.search)
    const vconsole = searchParams.get('vconsole')
    if (vconsole == vconsoleMd5) {
        // 当 URL 中包含 ?vconsole=47b1e3a9d33e6064e58cc4796c708447 参数时（参数值与 vconsoleMd5 匹配），自动加载 vConsole
        // 使用动态 import() 语法，避免 vConsole 代码打包到主 Bundle 中
        const module: any = await import('vconsole')
        const Vconsole = module.default
        const vConsole = new Vconsole()
        return vConsole
    }

    // #endif
}
