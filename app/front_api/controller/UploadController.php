<?php

namespace app\front_api\controller;

use app\common\enum\FileEnum;
use app\common\service\UploadService;
use Exception;
use think\response\Json;

/**
 * 上传文件
 * @class UploadController
 * @package app\front_api\controller
 * @author LZH
 * @date 2025/2/19
 */
class UploadController extends BaseApiController
{

    /**
     * 上传图片
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function image()
    {
        try {
            $result = UploadService::image(0, $this->userId,FileEnum::SOURCE_USER);
            return $this->success('上传成功', $result);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

}