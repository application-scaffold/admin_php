<?php

class YxEnv
{
    /**
     * 环境变量数据
     * @var array
     */
    protected $data = [];
    protected $filePath = '';

    public function __construct()
    {
        //$this->data = $_ENV;
    }

    /**
     * 读取环境变量定义文件
     * @param $file 环境变量定义文件
     * @return void
     * @author LZH
     * @date 2025/2/20
     */
    public function load($file)
    {
        $this->filePath = $file;
        $env = parse_ini_file($file, true);
        $this->set($env);
    }

    public function makeEnv($file)
    {
        if(!file_exists($file)){
            try{
                touch($file);
            }catch (Exception $e){
                return;
            }
        }
    }

    /**
     * 获取环境变量值
     * @param $name 环境变量名
     * @param $default 默认值
     * @param $php_prefix
     * @return array|bool|mixed|string|null
     * @author LZH
     * @date 2025/2/20
     */
    public function get($name = null, $default = null, $php_prefix = true)
    {
        if (is_null($name)) {
            return $this->data;
        }

        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return $this->getEnv($name, $default, $php_prefix);
    }

    protected function getEnv($name, $default = null, $php_prefix = true)
    {
        if ($php_prefix) {
            $name = 'PHP_' . $name;
        }

        $result = getenv($name);

        if (false === $result) {
            return $default;
        }

        if ('false' === $result) {
            $result = false;
        } elseif ('true' === $result) {
            $result = true;
        }

        if ( !isset($this->data[$name])) {
            $this->data[$name] = $result;
        }

        return $result;
    }

    /**
     * 写入Env文件
     * @param $envFilePath
     * @param array $databaseEnv
     * @return void
     * @author LZH
     * @date 2025/2/20
     */
    public function putEnv($envFilePath, array $databaseEnv)
    {
        $applyDbEnv = [
            'DATABASE.HOSTNAME' => $databaseEnv['host'],
            'DATABASE.DATABASE' => $databaseEnv['name'],
            'DATABASE.USERNAME' => $databaseEnv['user'],
            'DATABASE.PASSWORD' => $databaseEnv['password'],
            'DATABASE.HOSTPORT' => $databaseEnv['port'],
            'DATABASE.PREFIX' => $databaseEnv['prefix'],
        ];

        $envLine = array_merge($this->data, $applyDbEnv);

        $content = '';
        $lastPrefix = '';

        global $uniqueSalt;

        foreach ($envLine as $index => $value) {

            if ($index == 'PROJECT.UNIQUE_IDENTIFICATION' && !empty($uniqueSalt)) {
                $value = $uniqueSalt;
            }

            @list($prefix, $key) = explode('.', $index);

            if ($prefix != $lastPrefix && $key != null) {
                if ($lastPrefix != '')
                    $content .= "\n";
                $content .= "[$prefix]\n";
                $lastPrefix = $prefix;
            }

            if ($prefix != $lastPrefix && $key == null) {
                $content .= "$index = \"$value\"\n";
            } else {
                $content .= "$key = \"$value\"\n";
            }
        }

        if (!empty($content)) {
            file_put_contents($envFilePath, $content);
        }
    }

    /**
     * 设置环境变量值
     * @param $env
     * @param $value
     * @return void
     * @author LZH
     * @date 2025/2/20
     */
    public function set($env, $value = null)
    {
        if (is_array($env)) {

            foreach ($env as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $k => $v) {
                        $this->data[$key . '.' . $k] = $v;
                    }
                } else {
                    $this->data[$key] = $val;
                }
            }
        } else {
            $name = strtoupper(str_replace('.', '_', $env));

            $this->data[$name] = $value;
        }
    }
}
