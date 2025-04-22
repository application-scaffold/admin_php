<?php

declare(strict_types=1);

// 应用公共文件
use app\common\service\FileService;
use think\helper\Str;
use think\model\Collection;

/**
 * 生成密码加密密钥
 * @param string $plaintext
 * @param string $salt
 * @return string
 * @author LZH
 * @date 2025/2/20
 */
function create_password(string $plaintext, string $salt) : string
{
    return md5($salt . md5($plaintext . $salt));
}


/**
 * 随机生成token值
 * @param int|string $extra
 * @return string
 * @author LZH
 * @date 2025/2/20
 */
function create_token(int|string $extra = '') : string
{
    $salt = env('project.unique_identification', 'likeadmin');
    $encryptSalt = md5( $salt . uniqid());
    return md5($salt . $extra . time() . $encryptSalt);
}

/**
 * 截取某字符字符串
 * @param string $str
 * @param string $symbol
 * @return string
 * @author LZH
 * @date 2025/2/20
 */
function substr_symbol_behind(string $str, string $symbol = '.') : string
{
    $result = strripos($str, $symbol);
    if ($result === false) {
        return $str;
    }
    return substr($str, $result + 1);
}


/**
 * 对比php版本
 * @param string $version
 * @return bool
 * @author LZH
 * @date 2025/2/20
 */
function compare_php(string $version) : bool
{
    return version_compare(PHP_VERSION, $version) >= 0;
}

/**
 * 检查文件是否可写
 * @param string $dir
 * @return bool
 * @author LZH
 * @date 2025/2/20
 */
function check_dir_write(string $dir = '') : bool
{
    $route = root_path() . '/' . $dir;
    return is_writable($route);
}


/**
 * 多级线性结构排序
 * 转换前：
 * [{"id":1,"pid":0,"name":"a"},{"id":2,"pid":0,"name":"b"},{"id":3,"pid":1,"name":"c"},
 * {"id":4,"pid":2,"name":"d"},{"id":5,"pid":4,"name":"e"},{"id":6,"pid":5,"name":"f"},
 * {"id":7,"pid":3,"name":"g"}]
 * 转换后：
 * [{"id":1,"pid":0,"name":"a","level":1},{"id":3,"pid":1,"name":"c","level":2},{"id":7,"pid":3,"name":"g","level":3},
 * {"id":2,"pid":0,"name":"b","level":1},{"id":4,"pid":2,"name":"d","level":2},{"id":5,"pid":4,"name":"e","level":3},
 * {"id":6,"pid":5,"name":"f","level":4}]
 * @param array $data 线性结构数组
 * @param string $sub_key_name
 * @param string $id_name 数组id名
 * @param string $parent_id_name 数组祖先id名
 * @param int $parent_id 此值请勿给参数
 * @return array
 */
function linear_to_tree(array $data, string $sub_key_name = 'sub', string $id_name = 'id', string $parent_id_name = 'pid', int $parent_id = 0): array
{
    $tree = [];
    foreach ($data as $row) {
        if ($row[$parent_id_name] == $parent_id) {
            $temp = $row;
            $child = linear_to_tree($data, $sub_key_name, $id_name, $parent_id_name, $row[$id_name]);
            if ($child) {
                $temp[$sub_key_name] = $child;
            }
            $tree[] = $temp;
        }
    }
    return $tree;
}


/**
 * 删除目标目录
 * @param string $path
 * @param bool $delDir
 * @return bool
 * @author LZH
 * @date 2025/2/20
 */
function del_target_dir(string $path, bool $delDir): bool
{
    //没找到，不处理
    if (!file_exists($path)) {
        return false;
    }

    //打开目录句柄
    $handle = opendir($path);
    if ($handle) {
        while (false !== ($item = readdir($handle))) {
            if ($item != "." && $item != "..") {
                if (is_dir("$path/$item")) {
                    del_target_dir("$path/$item", $delDir);
                } else {
                    unlink("$path/$item");
                }
            }
        }
        closedir($handle);
        if ($delDir) {
            return rmdir($path);
        }
    } else {
        if (file_exists($path)) {
            return unlink($path);
        }
    }
    return false;
}


/**
 * 下载文件
 * @param string $url
 * @param string $saveDir
 * @param string $fileName
 * @return string
 * @author LZH
 * @date 2025/2/20
 */
function download_file(string $url, string $saveDir, string $fileName): string
{
    if (!file_exists($saveDir)) {
        mkdir($saveDir, 0775, true);
    }
    $fileSrc = $saveDir . $fileName;
    file_exists($fileSrc) && unlink($fileSrc);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    $file = curl_exec($ch);
    curl_close($ch);
    $resource = fopen($fileSrc, 'a');
    fwrite($resource, $file);
    fclose($resource);
    if (filesize($fileSrc) == 0) {
        unlink($fileSrc);
        return '';
    }
    return $fileSrc;
}

/**
 * 去除内容图片域名
 * @param string $content
 * @return array|string|string[]|null
 * @author LZH
 * @date 2025/2/20
 */
function clear_file_domain(string $content): array|string|null
{
    $fileUrl = FileService::getFileUrl();
    $pattern = '/<img[^>]*\bsrc=["\']'.preg_quote($fileUrl, '/').'([^"\']+)["\']/i';
    return preg_replace($pattern, '<img src="$1"', $content);
}

/**
 * 设置内容图片域名
 * @param string $content
 * @return array|string|string[]|null
 * @author LZH
 * @date 2025/2/20
 */
function get_file_domain(string $content): array|string|null
{
    $fileUrl = FileService::getFileUrl();
    $imgPreg = '/(<img .*?src=")(?!https?:\/\/)([^"]*)(".*?>)/is';
    $videoPreg = '/(<video .*?src=")(?!https?:\/\/)([^"]*)(".*?>)/is';
    $content = preg_replace($imgPreg, "\${1}$fileUrl\${2}\${3}", $content);
    $content = preg_replace($videoPreg, "\${1}$fileUrl\${2}\${3}", $content);
    return $content;
}

/**
 * uri小写
 * @param array|string $data
 * @author LZH
 * @date 2025/2/20
 */
function lower_uri(array|string $data): array
{
    if (!is_array($data)) {
        $data = [$data];
    }
    return array_map(function ($item) {
        return strtolower(Str::camel($item));
    }, $data);
}


/**
 * 获取无前缀数据表名
 * @param string $tableName
 * @return mixed|string
 * @author LZH
 * @date 2025/2/20
 */
function get_no_prefix_table_name(string $tableName): mixed
{
    $tablePrefix = config('database.connections.mysql.prefix');
    $prefixIndex = strpos($tableName, $tablePrefix);
    if ($prefixIndex !== 0 || $prefixIndex === false) {
        return $tableName;
    }
    $tableName = substr_replace($tableName, '', 0, strlen($tablePrefix));
    return trim($tableName);
}


/**
 * 生成编码
 * @param string $table
 * @param string $field
 * @param string $prefix
 * @param int $randSuffixLength
 * @param array $pool
 * @return string
 * @author LZH
 * @date 2025/2/20
 */
function generate_sn(string $table, string $field, string $prefix = '', int $randSuffixLength = 4, array $pool = []) : string
{
    $suffix = '';
    for ($i = 0; $i < $randSuffixLength; $i++) {
        if (empty($pool)) {
            $suffix .= rand(0, 9);
        } else {
            $suffix .= $pool[array_rand($pool)];
        }
    }
    $sn = $prefix . date('YmdHis') . $suffix;
    if (app()->make($table)->where($field, $sn)->find()) {
        return generate_sn($table, $field, $prefix, $randSuffixLength, $pool);
    }
    return $sn;
}


/**
 * 格式化金额
 * @param float $float
 * @return int|mixed|string
 * @author LZH
 * @date 2025/2/20
 */
function format_amount(float $float): mixed
{
    if ($float == intval($float)) {
        return intval($float);
    } elseif ($float == sprintf('%.1f', $float)) {
        return sprintf('%.1f', $float);
    }
    return $float;
}
