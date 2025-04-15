import { ref } from 'vue'

/**
 * 防止函数重复执行的钩子
 * 该钩子主要用于防止异步函数在上一次调用完成前被再次调用，通过锁定机制确保函数在并发环境下的有序执行
 *
 * @param fn 需要被锁定执行的异步函数
 * @returns 返回一个对象，包含锁定状态和锁定执行的函数
 */
export function useLockFn(fn: (...args: any[]) => Promise<any>) {
    // 定义一个响应式的锁定状态，初始为false
    const isLock = ref(false)

    /**
     * 锁定执行的函数
     * 该函数会检查是否已经有一个相同的函数调用正在执行，如果是，则直接返回，不再执行
     * 如果没有正在执行的调用，则锁定状态，执行传入的函数，并在执行完毕后解锁状态
     *
     * @param args 传入函数的参数
     * @returns 返回传入函数的执行结果
     */
    const lockFn = async (...args: any[]) => {
        // 如果已经有相同的函数调用正在执行，则直接返回，不再执行
        if (isLock.value) return
        // 设置锁定状态为true，表示已经开始执行函数
        isLock.value = true
        try {
            // 执行传入的函数，并在执行完毕后解锁状态
            const res = await fn(...args)
            isLock.value = false
            return res
        } catch (e) {
            // 如果执行过程中发生异常，则解锁状态，并抛出异常
            isLock.value = false
            throw e
        }
    }
    // 返回锁定状态和锁定执行的函数
    return {
        isLock,
        lockFn
    }
}
