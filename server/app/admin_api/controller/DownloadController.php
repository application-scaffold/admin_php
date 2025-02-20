<?php

namespace app\admin_api\controller;


use app\common\cache\ExportCache;
use app\common\service\JsonService;

class DownloadController extends BaseAdminApiController
{

    public array $notNeedLogin = ['export'];

    /**
     * 导出文件
     * @return \think\response\File|\think\response\Json
     * @author LZH
     * @date 2025/2/20
     */
    public function export()
    {
        //获取文件缓存的key
        $fileKey = request()->get('file');

        //通过文件缓存的key获取文件储存的路径
        $exportCache = new ExportCache();
        $fileInfo = $exportCache->getFile($fileKey);

        if (empty($fileInfo)) {
            return JsonService::fail('下载文件不存在');
        }

        //下载前删除缓存
        $exportCache->delete($fileKey);

        return download($fileInfo['src'] . $fileInfo['name'], $fileInfo['name']);
    }
}