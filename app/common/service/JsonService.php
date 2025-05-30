<?php

declare(strict_types=1);

namespace app\common\service;


use app\common\enum\ExportEnum;
use app\common\lists\BaseDataLists;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsExtendInterface;
use think\Response;
use think\response\Json;
use think\exception\HttpResponseException;

class JsonService
{

    /**
     * 接口操作成功，返回信息
     * @param string $msg
     * @param array $data
     * @param int $code
     * @param int $show
     * @return Json
     * @author LZH
     * @date 2025/2/18
     */
    public static function success(string $msg = 'success', array $data = [], int $code = 1, int $show = 1): Json
    {
        return self::result($code, $show, $msg, $data);
    }

    /**
     * 接口操作失败，返回信息
     * @param string $msg
     * @param array $data
     * @param int $code
     * @param int $show
     * @return Json
     * @author LZH
     * @date 2025/2/18
     */
    public static function fail(string $msg = 'fail', array $data = [], int $code = 0, int $show = 1): Json
    {
        return self::result($code, $show, $msg, $data);
    }

    /**
     * 接口返回数据
     * @param $data
     * @return Json
     * @author LZH
     * @date 2025/2/18
     */
    public static function data($data): Json
    {
        return self::success('', $data, 1, 0);
    }

    /**
     * 接口返回信息
     * @param int $code
     * @param int $show
     * @param string $msg
     * @param array $data
     * @param int $httpStatus
     * @return Json
     * @author LZH
     * @date 2025/2/18
     */
    private static function result(int $code, int $show, string $msg = 'OK', array $data = [], int $httpStatus = 200): Json
    {
        $result = compact('code', 'show', 'msg', 'data');
        return json($result, $httpStatus);
    }

    /**
     * 抛出异常json
     * @param string $msg
     * @param array $data
     * @param int $code
     * @param int $show
     * @return Json
     * @author LZH
     * @date 2025/2/18
     */
    public static function throw(string $msg = 'fail', array $data = [], int $code = 0, int $show = 1): Json
    {
        $data = compact('code', 'show', 'msg', 'data');
        $response = Response::create($data, 'json', 200);
        throw new HttpResponseException($response);
    }

    /**
     * 数据列表
     * @param BaseDataLists $lists
     * @return Json
     * @author LZH
     * @date 2025/2/18
     */
    public static function dataLists(BaseDataLists $lists): Json
    {
        //获取导出信息
        if ($lists->export == ExportEnum::INFO && $lists instanceof ListsExcelInterface) {
            return self::data($lists->excelInfo());
        }

        //获取导出文件的下载链接
        if ($lists->export == ExportEnum::EXPORT && $lists instanceof ListsExcelInterface) {
            $exportDownloadUrl = $lists->createExcel($lists->setExcelFields(), $lists->lists());
            return self::success('', ['url' => $exportDownloadUrl], 2);
        }

        $data = [
            'lists' => $lists->lists(),
            'count' => $lists->count(),
            'page_no' => $lists->pageNo,
            'page_size' => $lists->pageSize,
        ];
        $data['extend'] = [];
        if ($lists instanceof ListsExtendInterface) {
            $data['extend'] = $lists->extend();
        }
        return self::success('', $data, 1, 0);
    }
}