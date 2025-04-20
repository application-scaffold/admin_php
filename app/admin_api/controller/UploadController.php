<?php
declare(strict_types=1);

namespace app\admin_api\controller;

use app\common\service\UploadService;
use Exception;
use think\response\Json;

/**
 * 上传文件
 * @class UploadController
 * @package app\admin_api\controller
 * @author LZH
 * @date 2025/2/20
 */
class UploadController extends BaseAdminApiController
{
    /**
     * 上传图片
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function image(): Json
    {
        try {
            $cid = $this->request->post('cid', 0);
            $result = UploadService::image($cid);
            return $this->success('上传成功', $result);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 上传视频
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function video(): Json
    {
        try {
            $cid = $this->request->post('cid', 0);
            $result = UploadService::video($cid);
            return $this->success('上传成功', $result);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 上传文件
     * @return Json
     * @author LZH
     * @date 2025/2/20
     */
    public function file(): Json
    {
        try {
            $cid = $this->request->post('cid', 0);
            $result = UploadService::file($cid);
            return $this->success('上传成功', $result);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

}
