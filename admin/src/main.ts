// 引入权限控制模块
import './permission'

// 引入全局样式
import './styles/index.scss'

// 注册SVG图标
import 'virtual:svg-icons-register'

// 引入Vue的createApp方法
import { createApp } from 'vue'

// 引入获取配置的API方法
import { getConfig } from './api/app'

// 引入根组件
import App from './App.vue'

// 引入自定义插件
import install from './install'

// 创建Vue应用实例
const app = createApp(App)

// 使用自定义插件
app.use(install)

// 将应用挂载到DOM元素#app上
app.mount('#app')

// 获取配置信息并执行回调
getConfig().then((res) => {
    // 定义控制台输出的艺术字
    const adminPhpArt = `
            _           _                  _           
   __ _  __| |_ __ ___ (_)_ __       _ __ | |__  _ __  
  / _\` |/ _\` | '_ \` _ \\| | '_ \\     | '_ \\| '_ \\| '_ \\ 
 | (_| | (_| | | | | | | | | | |    | |_) | | | | |_) |
  \\__,_|\\__,_|_| |_| |_|_|_| |_|____| .__/|_| |_| .__/ 
                              |_____|_|         |_|    
`

    // 在控制台输出版本信息，带有自定义样式
    console.log(
        `%c likeadmin %c v${res.version} `,
        'padding: 4px 1px; border-radius: 3px 0 0 3px; color: #fff; background: #bbb; font-weight: bold;',
        'padding: 4px 1px; border-radius: 0 3px 3px 0; color: #fff; background: #4A5DFF; font-weight: bold;'
    )

    // 在控制台输出艺术字，带有自定义颜色
    console.log(`%c ${adminPhpArt}`, 'color: #4A5DFF')
})
