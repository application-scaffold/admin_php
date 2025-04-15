import wx from 'weixin-js-sdk' // 微信官方JS-SDK
import { getWxCodeUrl } from '@/api/account' // 获取微信授权URL的API
import { isAndroid } from './client' // 判断客户端是否为Android
import { wxJsConfig } from '@/api/app' // 获取JS-SDK配置的API
import { objectToQuery } from './util' // 对象转URL查询参数工具
export enum UrlScene {
    LOGIN = 'login', // 微信登录
    PC_LOGIN = 'pcLogin', // PC端登录
    BIND_WX = 'bindWx', // 绑定微信
    BASE = 'base' // 基础场景
}

//封装了微信网页开发的核心功能
const wechatOa = {
    // 存储授权码和场景
    _authData: {
        code: '',
        scene: ''
    },
    // 设置授权数据
    setAuthData(data: any = {}) {
        this._authData = data
    },
    // 获取授权数据
    getAuthData() {
        return this._authData
    },
    // URL签名处理
    getSignLink() {
        // 处理当前URL用于微信签名（解决iOS/Android差异）
        // 微信JS-SDK签名要求当前页面的完整URL（不含#后的哈希部分）
        if (typeof window.signLink === 'undefined' || window.signLink === '') {
            window.signLink = location.href.split('#')[0]
        }
        // 平台差异：Android直接取当前URL，iOS可能需缓存window.signLink保证签名一致性
        return isAndroid() ? location.href.split('#')[0] : window.signLink
    },
    // 生成微信授权URL，构造带场景参数的URL，用于微信OAuth2.0授权
    getUrl(
        scene: UrlScene,
        scope = 'snsapi_userinfo',
        extra = {}
    ): Promise<void> {
        const currentUrl = `${location.href}${
            location.search ? '&' : '?'
        }scene=${scene || ''}&${objectToQuery(extra)}`
        return new Promise((resolve, reject) => {
            getWxCodeUrl({ // 生成微信授权URL
                url: currentUrl,
                scope
            }).then((res) => {
                location.href = res.url // 跳转微信授权页
                resolve()
            }, reject)
        })
    },
    config() { // 通过后端接口wxJsConfig获取签名配置，再调用wx.config初始化
        return new Promise((resolve, reject) => {
            wxJsConfig({
                url: this.getSignLink()
            }).then((res) => {
                wx.config({
                    ...res, // 包含appId, timestamp, nonceStr, signature等
                    success: () => {
                        resolve('success')
                    },
                    fail: (res: any) => {
                        reject('wx config is fail')
                    }
                })
            })
        })
    },
    miniProgram: wx.miniProgram,
    ready(): Promise<void> {
        return new Promise((resolve, reject) => {
            wx.ready(() => {
                resolve()
            })
            wx.error(() => {
                reject()
            })
        })
    },
    // 调用微信支付接口，需传入后端返回的支付参数
    pay(options: Record<any, any>) {
        return new Promise((resolve, reject) => {
            this.ready()
                .then(() => {
                    wx.chooseWXPay({
                        timestamp: options.timeStamp,
                        nonceStr: options.nonceStr,
                        package: options.package,
                        signType: options.signType,
                        paySign: options.paySign,
                        success: (res: any) => {
                            if (res.errMsg === 'chooseWXPay:ok') {
                                resolve(res)
                            } else {
                                reject(res.errMsg)
                            }
                        },
                        cancel: (res: any) => {
                            reject(res)
                        },
                        fail: (res: any) => {
                            reject(res)
                        }
                    })
                })
                .catch((err) => {
                    reject(err)
                })
        })
    },
    // 分享功能
    async share(options: Record<any, any>): Promise<void> {
        return new Promise((resolve, reject) => {
            this.ready() // 需在wx.ready后调用，确保SDK已初始化
                .then(() => {
                    const { title, link, imgUrl, desc } = options
                    // 同时设置朋友圈（updateTimelineShareData）和好友分享（updateAppMessageShareData）
                    const shareApi = [
                        'updateTimelineShareData',
                        'updateAppMessageShareData'
                    ]
                    for (const api of shareApi) {
                        wx[api]({
                            title: title,
                            link: link,
                            imgUrl: imgUrl,
                            desc: desc,
                            success() {
                                resolve()
                            },
                            fail() {
                                reject()
                            }
                        })
                    }
                })
                .catch(reject)
        })
    },
    // 获取用户地址（调用wx.openAddress）
    getAddress() {
        return new Promise((reslove, reject) => {
            this.ready().then(() => {
                wx.openAddress({
                    success: (res: any) => {
                        reslove(res)
                    },
                    fail: (res: any) => {
                        reject(res)
                    }
                })
            })
        })
    },
    // 获取地理位置（wx.getLocation，坐标系为gcj02）
    getLocation() {
        return new Promise((resolve, reject) => {
            this.ready().then(() => {
                wx.getLocation({
                    type: 'gcj02',
                    success: (res: any) => {
                        resolve(res)
                    },
                    fail: (res: any) => {
                        reject(res)
                    }
                })
            })
        })
    },
    // 隐藏指定菜单项
    hideMenuItems(menuList: string[]) {
        return new Promise((resolve, reject) => {
            this.ready().then(() => {
                wx.hideMenuItems({
                    menuList,
                    success: (res: any) => {
                        resolve(res)
                    },
                    fail: (res: any) => {
                        reject(res)
                    }
                })
            })
        })
    },
    // 显示指定菜单项
    showMenuItems(menuList: string[]) {
        return new Promise((resolve, reject) => {
            this.ready().then(() => {
                wx.showMenuItems({
                    menuList,
                    success: (res: any) => {
                        resolve(res)
                    },
                    fail: (res: any) => {
                        reject(res)
                    }
                })
            })
        })
    }
}

export default wechatOa
