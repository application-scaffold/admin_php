/* eslint-env node */
require('@rushstack/eslint-patch/modern-module-resolution')

/**
 * ESLint配置对象
 *
 * 该配置对象定义了项目的ESLint规则和设置，包括使用的插件、扩展规则以及自定义规则
 */
module.exports = {
    // 指定ESLint从当前目录开始查找配置文件，不再向上查找
    root: true,
    // 忽略指定的文件或目录，使其不受ESLint规则检查
    ignorePatterns: ['/auto-imports.d.ts', '/components.d.ts'],
    /**
     * 继承的ESLint规则集
     *
     * 包含Vue 3的基本规则、ESLint推荐规则、TypeScript推荐规则、Prettier规则以及自定义的自动导入规则
     */
    extends: [
        'plugin:vue/vue3-essential',
        'eslint:recommended',
        '@vue/eslint-config-typescript/recommended',
        '@vue/eslint-config-prettier',
        './.eslintrc-auto-import.json'
    ],
    // 使用的ESLint插件列表
    plugins: ['simple-import-sort'],
    /**
     * 自定义ESLint规则
     *
     * 定义了项目中特定的编码规范和错误处理方式
     */
    rules: {
        // 强制导入语句按字母顺序排序
        'simple-import-sort/imports': 'error', // 强制导入语句排序
        /**
         * 配置Prettier规则，使其与ESLint规则兼容
         *
         * 包含代码格式化选项，如是否使用分号、单引号、行宽等
         */
        'prettier/prettier': [
            'warn',
            {
                semi: false,
                singleQuote: true,
                printWidth: 100,
                proseWrap: 'preserve',
                bracketSameLine: false,
                endOfLine: 'lf',
                tabWidth: 4,
                useTabs: false,
                trailingComma: 'none'
            }
        ],
        // 关闭多单词组件名称的强制要求
        'vue/multi-word-component-names': 'off',
        // 允许使用any类型
        '@typescript-eslint/no-explicit-any': 'off',
        // 允许使用ts-ignore注释
        '@typescript-eslint/ban-ts-comment': 'off',
        // 关闭未定义变量的警告
        'no-undef': 'off',
        // 关闭从Vue导入的推荐方式
        'vue/prefer-import-from-vue': 'off',
        // 关闭对Object.prototype内置方法的警告
        'no-prototype-builtins': 'off',
        // 关闭对扩展运算符的偏好
        'prefer-spread': 'off',
        // 允许使用非空断言
        '@typescript-eslint/no-non-null-assertion': 'off',
        // 允许使用非空断言的可选链
        '@typescript-eslint/no-non-null-asserted-optional-chain': 'off'
    },
    /**
     * 定义全局变量
     *
     * 将module变量定义为只读，避免在代码中对其进行修改
     */
    globals: {
        module: 'readonly'
    }
}
