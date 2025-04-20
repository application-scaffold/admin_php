<?php
declare(strict_types=1);

namespace app\front_api\controller;


use app\front_api\logic\IndexLogic;
use think\response\Json;


/**
 * @OA\Info(
 * title="前端 API 文档",
 * version="1.0.0",
 * description="前端访问的 API 接口",
 * @OA\Contact(
 * email="your.email@example.com"
 * ),
 * @OA\License(
 * name="Apache 2.0",
 * url="https://www.apache.org/licenses/LICENSE-2.0.html"
 * )
 * )
 * @OA\Server(
 * url="/api/v1",
 * description="API 服务器"
 * )
 * @OA\SecurityScheme(
 * securityScheme="bearerAuth",
 * type="http",
 * scheme="bearer"
 * )
 */
class IndexController extends BaseApiController
{

    public array $notNeedLogin = ['index', 'config', 'policy', 'decorate'];

    /**
     * @OA\Get(
     *     path="/api/v1/index",
     *     summary="首页数据接口",
     *     description="获取首页聚合数据(轮播图+推荐列表)",
     *     operationId="index",
     *     tags={"首页"},
     *     @OA\Response(
     *         response=200,
     *         description="成功响应",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="banners",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="image", type="string", example="http://cdn.example.com/banner1.jpg"),
     *                         @OA\Property(property="link", type="string", example="/detail/1")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="recommendations",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="精品推荐"),
     *                         @OA\Property(property="price", type="number", format="float", example="99.99")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="服务器异常",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=500),
     *             @OA\Property(property="message", type="string", example="服务器错误")
     *         )
     *     )
     * )
     */
    public function index(): Json
    {
        $result = IndexLogic::getIndexData();
        return $this->data($result);
    }


    /**
     * 全局配置
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function config(): Json
    {
        $result = IndexLogic::getConfigData();
        return $this->data($result);
    }


    /**
     * 政策协议
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function policy(): Json
    {
        $type = $this->request->get('type/s', '');
        $result = IndexLogic::getPolicyByType($type);
        return $this->data($result);
    }

    /**
     * 装修信息
     * @return Json
     * @author LZH
     * @date 2025/2/19
     */
    public function decorate(): Json
    {
        $id = $this->request->get('id/d');
        $result = IndexLogic::getDecorate($id);
        return $this->data($result);
    }

}