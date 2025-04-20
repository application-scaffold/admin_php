<?php

declare(strict_types=1);

namespace app\common\validate;

use app\common\service\JsonService;
use think\Validate;

class BaseValidate extends Validate
{
    public string $method = 'GET';

    /**
     * 设置请求方式
     * @return $this
     * @author LZH
     * @date 2025/2/19
     */
    public function post()
    {
        if (!$this->request->isPost()) {
            JsonService::throw('请求方式错误，请使用post请求方式');
        }
        $this->method = 'POST';
        return $this;
    }

    /**
     * 设置请求方式
     * @return $this
     * @author LZH
     * @date 2025/2/19
     */
    public function get()
    {
        if (!$this->request->isGet()) {
            JsonService::throw('请求方式错误，请使用get请求方式');
        }
        return $this;
    }


    /**
     * 切面验证接收到的参数
     * @param $scene
     * @param array $validateData
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function goCheck($scene = null, array $validateData = []): array
    {
        //接收参数
        if ($this->method == 'GET') {
            $params = request()->get();
        } else {
            $params = request()->post();
        }
        //合并验证参数
        $params = array_merge($params, $validateData);

        //场景
        if ($scene) {
            $result = $this->scene($scene)->check($params);
        } else {
            $result = $this->check($params);
        }

        if (!$result) {
            $exception = is_array($this->error) ? implode(';', $this->error) : $this->error;
            JsonService::throw($exception);
        }
        // 3.成功返回数据
        return $params;
    }
}