import fsExtra from 'fs-extra'
import path from 'path'

const { existsSync, remove, copy } = fsExtra
const cwd = process.cwd()
//打包发布路径，谨慎改动
const releaseRelativePath = '../server/public/admin'
const distPath = path.resolve(cwd, 'dist')
const releasePath = path.resolve(cwd, releaseRelativePath)

/**
 * 异步构建函数，用于处理文件复制操作
 * 如果发布目录已存在，则先将其删除，然后将dist目录复制到发布目录
 * 此函数确保了在构建新版本之前，之前的发布内容已被清理，避免了冗余文件的累积
 */
async function build() {
    // 检查发布路径是否存在，如果存在则将其删除，确保之前的内容不会影响到新的构建
    if (existsSync(releasePath)) {
        await remove(releasePath)
    }

    // 开始复制文件，打印复制开始的提示信息
    console.log(`文件正在复制 ==> ${releaseRelativePath}`)

    // 尝试将dist目录下的文件复制到发布路径，如果发生错误则捕获并打印错误信息
    try {
        await copyFile(distPath, releasePath)
    } catch (error) {
        console.log(`\n ${error}`)
    }
    // 完成文件复制后，打印复制完成的提示信息
    console.log(`文件已复制 ==> ${releaseRelativePath}`)
}

/**
 * 异步复制文件或目录
 *
 * 该函数使用Promise封装了文件或目录的复制过程，以便于在异步操作中进行错误处理和流程控制
 *
 * @param {string} sourceDir - 源文件或目录的路径这是复制操作的起始点
 * @param {string} targetDir - 目标文件或目录的路径这是复制操作的目的地
 * @returns {Promise} 返回一个Promise对象，用于在复制操作完成时进行异步处理如果复制过程中发生错误，Promise将被拒绝（reject）；否则，Promise将被解决（resolve）
 */
function copyFile(sourceDir, targetDir) {
    // 使用copy函数执行实际的复制操作，复制完成后通过回调函数处理结果
    return new Promise((resolve, reject) => {
        // 使用copy函数执行实际的复制操作，复制完成后通过回调函数处理结果
        copy(sourceDir, targetDir, (err) => {
            if (err) {
                // 复制过程中发生错误，通过Promise的reject方法传递错误对象
                reject(err)
            } else {
                // 复制成功，通过Promise的resolve方法表示操作完成
                resolve()
            }
        })
    })
}

build()
