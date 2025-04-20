<?php

declare(strict_types=1);

namespace app\common\service;

use app\common\model\Config;

class ConfigService
{
    /**
     * 设置配置值
     * @param string $type
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @author LZH
     * @date 2025/2/18
     */
    public static function set(string $type, string $name, mixed $value): mixed
    {
        $original = $value;
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        $data = Config::where(['type' => $type, 'name' => $name])->findOrEmpty();

        if ($data->isEmpty()) {
            Config::create([
                'type' => $type,
                'name' => $name,
                'value' => $value,
            ]);
        } else {
            $data->value = $value;
            $data->save();
        }

        // 返回原始值
        return $original;
    }

    /**
     * 获取配置值
     * @param string $type
     * @param string $name
     * @param mixed $default_value
     * @return array|int|mixed|string|void
     * @author LZH
     * @date 2025/2/18
     */
    public static function get(string $type, string $name = '', mixed $default_value = null): mixed
    {
        if (!empty($name)) {
            $value = Config::where(['type' => $type, 'name' => $name])->value('value');
            if (!is_null($value)) {
                $json = json_decode($value, true);
                $value = json_last_error() === JSON_ERROR_NONE ? $json : $value;
            }
            if ($value) {
                return $value;
            }
            // 返回特殊值 0 '0'
            if ($value === 0 || $value === '0') {
                return $value;
            }
            // 返回默认值
            if ($default_value !== null) {
                return $default_value;
            }
            // 返回本地配置文件中的值
            return config('project.' . $type . '.' . $name);
        }

        // 取某个类型下的所有name的值
        $data = Config::where(['type' => $type])->column('value', 'name');
        foreach ($data as $k => $v) {
            $json = json_decode($v, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data[$k] = $json;
            }
        }
        if ($data) {
            return $data;
        }
    }
}