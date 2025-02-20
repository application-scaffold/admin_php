# Vue 项目

该模板将帮助您使用 Vue 3 和 Vite 开始开发。

## 推荐的 IDE 设置

[VSCode](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar)（并禁用 Vetur） + [TypeScript Vue Plugin (Volar)](https://marketplace.visualstudio.com/items?itemName=Vue.vscode-typescript-vue-plugin)。

## 在 TypeScript 中支持 `.vue` 文件的类型

默认情况下，TypeScript 无法处理 `.vue` 导入的类型信息，因此我们使用 `vue-tsc` 替换 `tsc` CLI 进行类型检查。在编辑器中，我们需要 [TypeScript Vue Plugin (Volar)](https://marketplace.visualstudio.com/items?itemName=Vue.vscode-typescript-vue-plugin) 来让 TypeScript 语言服务识别 `.vue` 文件的类型。

如果您觉得独立的 TypeScript 插件不够快，Volar 还实现了一种更高效的 [Take Over Mode](https://github.com/johnsoncodehk/volar/discussions/471#discussioncomment-1361669)。您可以通过以下步骤启用它：

1. 禁用内置的 TypeScript 扩展
   1. 在 VSCode 的命令面板中运行 `Extensions: Show Built-in Extensions`
   2. 找到 `TypeScript and JavaScript Language Features`，右键点击并选择 `Disable (Workspace)`
2. 在命令面板中运行 `Developer: Reload Window` 重新加载 VSCode 窗口。

## 自定义配置

参考 [Vite 配置文档](https://vitejs.dev/config/)。

## 项目设置

```sh
npm install
```

### 开发环境编译和热重载

```sh
npm run dev
```

### 类型检查、编译和生产环境压缩

```sh
npm run build
```

### 使用 [ESLint](https://eslint.org/) 进行代码检查

```sh
npm run lint
```

## 开发阶段配置

由于 cros 同源策略，存在两种方式绕过：

1. vite server 设置 proxy

   .env.development 文件中：

   ```json
   # 必须为空，表示和vite启动服务器同域名
   VITE_APP_BASE_URL=''
   ```
   vite.config.ts 文件中：
   ```json
    server: {
        host: '0.0.0.0',
        proxy: {
            // 将 `/api` 开头的请求代理到目标服务器
            '/admin_api': {
                target: 'http://demo.myadmin.com', // 目标服务器地址
                changeOrigin: true,              // 允许跨域
                rewrite: (path) => path,          // 不重写路径，保留 api 前缀
                // rewrite: (path) => path.replace(/^\/admin_api/, ''), // 路径重写，移除 `/api` 前缀
            },
        }
    },
   ```

2. tp 后端启用 cros 中间件

   app目录中 middleware.php 文件中
   
   ```php
   <?php
   // 全局中间件定义文件
   return [
       app\common\http\middleware\CorsAllowMiddleware::class,
       //基础中间件
       app\common\http\middleware\BaseMiddleware::class,
   ];
   ```
配置启动任何一种方式即可。

