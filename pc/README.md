# Nuxt 3

查看 [Nuxt 3 文档](https://v3.nuxtjs.org) 了解更多信息。

## 安装

确保安装所有依赖项：

```bash
# 使用 yarn
yarn install

# 使用 npm
npm install

# 使用 pnpm
pnpm install --shamefully-hoist
```

## 开发服务器

启动开发服务器，访问地址为 http://localhost:3000：

```bash
npm run dev
```

## 生产环境

为生产环境构建应用：

```bash
npm run build
```

本地预览生产环境构建：

```bash
npm run preview
```

查看 [部署文档](https://v3.nuxtjs.org/guide/deploy/presets) 获取更多信息。

## 项目规范

- 使用了 eslint 去检查代码规范
- 使用 prettier 去格式化代码

## 目录结构

```text
├── pc                               # 源代码
│  ├── api                           # 所有请求
│  ├── assets                        # 字体，图片等静态资源
│  ├── components                    # 全局公用组件
│  ├── composables                   # 
│  ├── constants                     # 常量
│  ├── enums                         # 全局枚举
│  ├── layouts                       # 布局组件
│  ├── middleware                    # 中间件
│  ├── nuxt                          # nuxt 相关文件
│  ├── pages                         # nuxt 页面
│  ├── plugins                       # 插件
│  ├── public                        # public目录
│  ├── scripts                       # 构建脚本
│  ├── stores                        # 全局状态管理
│  ├── stores                        # 存储目录
│  ├── typings                       # ts声明文件
│  ├── utils                         # 工具目录
│  ├── .env.xxx                      # 环境变量配置
│  ├── .eslintrc.cjs                 # eslint 配置项
│  ├── .prettierrc                   # pretty 配置项
│  ├── App.vue                       # 入口页面
│  ├── global.d.ts                   # 全局声明文件
│  ├── nuxt.config.ts                # nuxt 配置文件
│  ├── package.json                  # package.json
│  ├── tailwind.config.js            # tailwindcss 配置项
│  └── tsconfig.json                 # ts 配置项
```




