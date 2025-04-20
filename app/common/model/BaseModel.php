<?php
declare (strict_types = 1);

namespace app\common\model;

use app\common\service\FileService;
use think\Model;

/**
 * 基础模型
 * @class BaseModel
 * @package app\common\model
 * @author LZH
 * @date 2025/2/18
 */
class BaseModel extends Model
{

    /**
     * 公共处理图片,补全路径
     * @param string $value
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getImageAttr(string $value): string
    {
        return trim($value) ? FileService::getFileUrl($value) : '';
    }

    /**
     * 公共图片处理,去除图片域名
     * @param string $value
     * @return mixed|string
     * @author LZH
     * @date 2025/2/18
     */
    public function setImageAttr(string $value): mixed
    {
        return trim($value) ? FileService::setFileUrl($value) : '';
    }
}