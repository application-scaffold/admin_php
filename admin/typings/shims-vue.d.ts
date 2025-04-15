declare module '*.vue' {
    import type { DefineComponent } from 'vue'
    // 使用更明确的类型替代 {}
    const component: DefineComponent<Record<string, never>, Record<string, never>, any>
    export default component
}
