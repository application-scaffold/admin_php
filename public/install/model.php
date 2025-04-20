<?php

/**
 * 安装界面需要的各种模块
 * @class installModel
 * @package ${NAMESPACE}
 * @author LZH
 * @date 2025/2/20
 */
class installModel
{
    private $host;
    /**
     * @var string 数据库名称
     */
    private $name;
    private $user;
    private $encoding;
    private $password;
    private $port;
    private $prefix;
    private $successTable = [];
    /**
     * @var bool
     */
    private $allowNext = true;
    /**
     * @var PDO|string
     */
    private $dbh = null;
    /**
     * @var bool
     */
    private $clearDB = false;

    /**
     * php版本
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function getPhpVersion()
    {
        return PHP_VERSION;
    }

    /**
     * 当前版本是否符合
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkPHP()
    {
        return $result = version_compare(PHP_VERSION, '8.0.0') >= 0 ? 'ok' : 'fail';
    }

    /**
     * 是否有PDO
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkPDO()
    {
        return $result = extension_loaded('pdo') ? 'ok' : 'fail';
    }

    /**
     * 是否有PDO::MySQL
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkPDOMySQL()
    {
        return $result = extension_loaded('pdo_mysql') ? 'ok' : 'fail';
    }

    /**
     * 是否支持JSON
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkJSON()
    {
        return $result = extension_loaded('json') ? 'ok' : 'fail';
    }

    /**
     * 是否支持openssl
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkOpenssl()
    {
        return $result = extension_loaded('openssl') ? 'ok' : 'fail';
    }

    /**
     * 是否支持mbstring
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkMbstring()
    {
        return $result = extension_loaded('mbstring') ? 'ok' : 'fail';
    }

    /**
     * 是否支持zlib
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkZlib()
    {
        return $result = extension_loaded('zlib') ? 'ok' : 'fail';
    }

    /**
     * 是否支持curl
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkCurl()
    {
        return $result = extension_loaded('curl') ? 'ok' : 'fail';
    }

    /**
     * 检查GD2扩展
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkGd2()
    {
        return $result = extension_loaded('gd') ? 'ok' : 'fail';
    }

    /**
     * 检查Dom扩展
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkDom()
    {
        return $result = extension_loaded('dom') ? 'ok' : 'fail';
    }

    /**
     * 是否支持filter
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkFilter()
    {
        return $result = extension_loaded('filter') ? 'ok' : 'fail';
    }

    /**
     * 是否支持iconv
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkIconv()
    {
        return $result = extension_loaded('iconv') ? 'ok' : 'fail';
    }


    /**
     * 检查fileinfo扩展
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkFileInfo()
    {
        return $result = extension_loaded('fileinfo') ? 'ok' : 'fail';
    }

    /**
     * 取得临时目录路径
     * @return array
     * @author LZH
     * @date 2025/2/20
     */
    public function getTmpRoot()
    {
        $path = $this->getAppRoot() . '/runtime';
        return [
            'path'     => $path,
            'exists'   => is_dir($path),
            'writable' => is_writable($path),
        ];
    }

    /**
     * 检查临时路径
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkTmpRoot()
    {
        $tmpRoot = $this->getTmpRoot()['path'];
        return $result = (is_dir($tmpRoot) and is_writable($tmpRoot)) ? 'ok' : 'fail';
    }

    /**
     * SESSION路径是否可写
     * @return array
     * @author LZH
     * @date 2025/2/20
     */
    public function getSessionSavePath()
    {
        $sessionSavePath = preg_replace("/\d;/", '', session_save_path());

        return [
            'path'     => $sessionSavePath,
            'exists'   => is_dir($sessionSavePath),
            'writable' => is_writable($sessionSavePath),
        ];
    }

    /**
     * 检查session路径可写状态
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkSessionSavePath()
    {
        $sessionSavePath = preg_replace("/\d;/", '', session_save_path());
        $result = (is_dir($sessionSavePath) and is_writable($sessionSavePath)) ? 'ok' : 'fail';
        if ($result == 'fail') return $result;

        file_put_contents($sessionSavePath . '/zentaotest', 'zentao');
        $sessionContent = file_get_contents($sessionSavePath . '/zentaotest');
        if ($sessionContent == 'zentao') {
            unlink($sessionSavePath . '/zentaotest');
            return 'ok';
        }
        return 'fail';
    }

    /**
     * 取得data目录是否可选
     * @return array
     * @author LZH
     * @date 2025/2/20
     */
    public function getDataRoot()
    {
        $path = $this->getAppRoot();
        return [
            'path'     => $path . 'www' . DS . 'data',
            'exists'   => is_dir($path),
            'writable' => is_writable($path),
        ];
    }

    /**
     * 取得root路径
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkDataRoot()
    {
        $dataRoot = $this->getAppRoot() . 'www' . DS . 'data';
        return $result = (is_dir($dataRoot) and is_writable($dataRoot)) ? 'ok' : 'fail';
    }

    /**
     * 取得php.ini信息
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function getIniInfo()
    {
        $iniInfo = '';
        ob_start();
        phpinfo(1);
        $lines = explode("\n", strip_tags(ob_get_contents()));
        ob_end_clean();
        foreach ($lines as $line) if (strpos($line, 'ini') !== false) $iniInfo .= $line . "\n";
        return $iniInfo;
    }


    /**
     * 创建安装锁定文件
     * @return bool
     * @author LZH
     * @date 2025/2/20
     */
    public function mkLockFile()
    {
        return touch($this->getAppRoot() . '/config/install.lock');
    }

    /**
     * 检查之前是否有安装
     * @return bool
     * @author LZH
     * @date 2025/2/20
     */
    public function appIsInstalled()
    {
        return file_exists($this->getAppRoot() . '/config/install.lock');
    }

    /**
     * 取得配置信息
     * @param $dbName
     * @param $connectionInfo
     * @return stdclass
     * @throws Exception
     * @author LZH
     * @date 2025/2/20
     */
    public function checkConfig($dbName, $connectionInfo)
    {
        $return = new stdclass();
        $return->result = 'ok';

        /* Connect to database. */
        $this->setDBParam($connectionInfo);
        $this->dbh = $this->connectDB();
        if (strpos($dbName, '.') !== false) {
            $return->result = 'fail';
            $return->error = '没有发现数据库信息';
            return $return;
        }
        if ( !is_object($this->dbh)) {
            $return->result = 'fail';
            $return->error = '安装错误，请检查连接信息:'.mb_strcut($this->dbh,0,30).'...';
            echo $this->dbh;
            return $return;
        }

        /* Get mysql version. */
        $version = $this->getMysqlVersion();

        /* check mysql sql_model */
//        if(!$this->checkSqlMode($version)) {
//            $return->result = 'fail';
//            $return->error = '请在mysql配置文件修改sql-mode添加NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
//            return $return;
//        }

        /* If database no exits, try create it. */
        if ( !$this->dbExists()) {
            if ( !$this->createDB($version)) {
                $return->result = 'fail';
                $return->error = '创建数据库错误';
                return $return;
            }
        } elseif ($this->tableExits() and $this->clearDB == false) {
            $return->result = 'fail';
            $return->error = '数据表已存在，您之前可能已安装本系统，如需继续安装请选择新的数据库。';
            return $return;
        } elseif ($this->dbExists() and $this->clearDB == true) {
            if (!$this->dropDb($connectionInfo['name'])) {
                $return->result = 'fail';
                $return->error = '数据表已经存在，删除已存在库错误,请手动清除';
                return $return;
            } else {
                if ( !$this->createDB($version)) {
                    $return->result = 'fail';
                    $return->error = '创建数据库错误!';
                    return $return;
                }
            }
        }

        /* Create tables. */
        if ( !$this->createTable($version, $connectionInfo)) {
            $return->result = 'fail';
            $return->error = '创建表格失败';
            return $return;
        }

        return $return;
    }

    /**
     * 设置数据库相关信息
     * @param $post
     * @return void
     * @author LZH
     * @date 2025/2/20
     */
    public function setDBParam($post)
    {
        $this->host = $post['host'];
        $this->name = $post['name'];
        $this->user = $post['user'];
        $this->encoding = 'utf8mb4';
        $this->password = $post['password'];
        $this->port = $post['port'];
        $this->prefix = $post['prefix'];
        $this->clearDB = $post['clear_db'] == 'on';
    }

    /**
     * 连接数据库
     * @return PDO|string
     * @author LZH
     * @date 2025/2/20
     */
    public function connectDB()
    {
        $dsn = "mysql:host={$this->host}; port={$this->port}";
        try {
            $dbh = new PDO($dsn, $this->user, $this->password);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->exec("SET NAMES {$this->encoding}");
            $dbh->exec("SET NAMES {$this->encoding}");
            try{
                $dbh->exec("SET GLOBAL sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';");
            }catch (Exception $e){

            }
            return $dbh;
        } catch (PDOException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * 检查数据库是否存在
     * @return mixed
     * @author LZH
     * @date 2025/2/20
     */
    public function dbExists()
    {
        $sql = "SHOW DATABASES like '{$this->name}'";
        return $this->dbh->query($sql)->fetch();
    }

    /**
     * 检查表是否存在
     * @return mixed
     * @author LZH
     * @date 2025/2/20
     */
    public function tableExits()
    {
        $configTable = sprintf("'%s'", $this->prefix . TESTING_TABLE);
        $sql = "SHOW TABLES FROM {$this->name} like $configTable";
        return $this->dbh->query($sql)->fetch();
    }

    /**
     * 获取mysql版本号
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function getMysqlVersion()
    {
        $sql = "SELECT VERSION() AS version";
        $result = $this->dbh->query($sql)->fetch();
        return substr($result->version, 0, 3);
    }

    /**
     * 检测数据库sql_mode
     * @param $version
     * @return bool
     * @author LZH
     * @date 2025/2/20
     */
    public function checkSqlMode($version)
    {
        $sql = "SELECT @@global.sql_mode";
        $result = $this->dbh->query($sql)->fetch();
        $result = (array)$result;

        if ($version >= 5.7 && $version < 8.0) {
            if ((strpos($result['@@global.sql_mode'],'NO_AUTO_CREATE_USER') !== false)
                && (strpos($result['@@global.sql_mode'],'NO_ENGINE_SUBSTITUTION') !== false)) {
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * 创建数据库
     * @param $version
     * @return false|PDOStatement
     * @author LZH
     * @date 2025/2/20
     */
    public function createDB($version)
    {
        $sql = "CREATE DATABASE `{$this->name}`";
        if ($version > 4.1) $sql .= " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
        return $this->dbh->query($sql);
    }

    /**
     * 创建表
     * @param $version
     * @param $post
     * @return bool
     * @throws \Random\RandomException
     * @author LZH
     * @date 2025/2/20
     */
    public function createTable($version, $post)
    {
        $dbFile = $this->getInstallRoot() . '/db/like.sql';
        //file_put_contents($dbFile, $this->initAccount($post), FILE_APPEND);
        $content = str_replace(";\r\n", ";\n", file_get_contents($dbFile));
        $tables = explode(";\n", $content);
        $tables[] = $this->initAccount($post);
        $installTime = microtime(true) * 10000;

        foreach ($tables as $table) {
            $table = trim($table);
            if (empty($table)) continue;

            if (strpos($table, 'CREATE') !== false and $version <= 4.1) {
                $table = str_replace('DEFAULT CHARSET=utf8', '', $table);
            }
//            elseif (strpos($table, 'DROP') !== false and $this->clearDB != false) {
//                $table = str_replace('--', '', $table);
//            }

            /* Skip sql that is note. */
            if (strpos($table, '--') === 0) continue;

            $table = str_replace('`la_', $this->name . '.`la_', $table);
            $table = str_replace('`la_', '`' . $this->prefix, $table);

            if (strpos($table, 'CREATE') !== false) {
                $tableName = explode('`', $table)[1];
                $installTime += random_int(3000, 7000);
                $this->successTable[] = [$tableName, date('Y-m-d H:i:s', intdiv($installTime , 10000))];
            }

//            if (strpos($table, "INSERT INTO ") !== false) {
//                $table = str_replace('INSERT INTO ', 'INSERT INTO ' .$this->name .'.', $table);
//            }

            try {
                if ( !$this->dbh->query($table)) return false;
            } catch (Exception $e) {
                echo 'error sql: ' . $table . "<br>";
                echo $e->getMessage() . "<br>";
                return false;
            }
        }
        return true;
    }

    /**
     * 删除数据库
     * @param $db
     * @return false|PDOStatement
     * @author LZH
     * @date 2025/2/20
     */
    public function dropDb($db)
    {
        $sql = "drop database {$db};";
        return $this->dbh->query($sql);
    }

    /**
     * 取得安装成功的表列表
     * @return array
     * @author LZH
     * @date 2025/2/20
     */
    public function getSuccessTable()
    {
        return $this->successTable;
    }

    /**
     * 创建演示数据
     * @return bool
     * @author LZH
     * @date 2025/2/20
     */
    public function importDemoData()
    {
        $demoDataFile = 'ys.sql';
        $demoDataFile = $this->getInstallRoot() . '/db/' . $demoDataFile;
        if (!is_file($demoDataFile)) {
            echo "<br>";
            echo 'no file:' .$demoDataFile;
            return false;
        }
        $content = str_replace(";\r\n", ";\n", file_get_contents($demoDataFile));
        $insertTables = explode(";\n", $content);
        foreach ($insertTables as $table) {
            $table = trim($table);
            if (empty($table)) continue;

            $table = str_replace('`la_', $this->name . '.`la_', $table);
            $table = str_replace('`la_', '`' .$this->prefix, $table);
            if ( !$this->dbh->query($table)) return false;
        }

        // 移动图片资源，因为数据库中可能存在图片链接
        $this->cpFiles($this->getInstallRoot().'/uploads', $this->getAppRoot().'/public/uploads');

        return true;
    }

    /**
     * 将一个文件夹下的所有文件及文件夹
     * 复制到另一个文件夹里（保持原有结构）
     *
     * @param <string> $rootFrom 源文件夹地址（最好为绝对路径）
     * @param <string> $rootTo 目的文件夹地址（最好为绝对路径）
     */
    function cpFiles($rootFrom, $rootTo){

            $handle = opendir($rootFrom);
            while (false !== ($file = readdir($handle))) {
                //DIRECTORY_SEPARATOR 为系统的文件夹名称的分隔符 例如：windos为'/'; linux为'/'
                $fileFrom = $rootFrom . DIRECTORY_SEPARATOR . $file;
                $fileTo = $rootTo . DIRECTORY_SEPARATOR . $file;
                if ($file == '.' || $file == '..') {
                    continue;
                }

                    if (is_dir($fileFrom)) {
                        if (!is_dir($fileTo)) { //目标目录不存在则创建
                            mkdir($fileTo, 0777);
                        }
                        $this->cpFiles($fileFrom, $fileTo);
                    } else {
                        if (!file_exists($fileTo)) {
                            @copy($fileFrom, $fileTo);
                            if (strstr($fileTo, "access_token.txt")) {
                                chmod($fileTo, 0777);
                            }
                        }
                    }

            }
    }

    /**
     * 当前应用程序的相对路径
     * @return false|string
     * @author LZH
     * @date 2025/2/20
     */
    public function getAppRoot()
    {
        return realpath($this->getInstallRoot() . '/../../');
    }

    /**
     * 获取安装目录
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function getInstallRoot()
    {
        return INSTALL_ROOT;
    }

    /**
     * 目录的容量
     * @param $dir
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function freeDiskSpace($dir)
    {
        // M
        $freeDiskSpace = disk_free_space(realpath(__DIR__)) / 1024 / 1024;

        // G
        if ($freeDiskSpace > 1024) {
            return number_format($freeDiskSpace / 1024, 2) . 'G';
        }

        return number_format($freeDiskSpace, 2) . 'M';
    }

    /**
     * 获取状态标志
     * @param $statusSingle
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function correctOrFail($statusSingle)
    {
        if ($statusSingle == 'ok')
            return '<td class="layui-icon green">&#xe605;</td>';
        $this->allowNext = false;
        return '<td class="layui-icon wrong">&#x1006;</td>';
    }

    /**
     * 是否允许下一步
     * @return bool
     * @author LZH
     * @date 2025/2/20
     */
    public function getAllowNext()
    {
        return $this->allowNext;
    }

    /**
     * 检查session auto start
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkSessionAutoStart()
    {
        return $result = ini_get('session.auto_start') == '0' ? 'ok' : 'fail';
    }

    /**
     * 检查auto tags
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkAutoTags()
    {
        return $result = ini_get('session.auto_start') == '0' ? 'ok' : 'fail';
    }

    /**
     * 检查目录是否可写
     * @param $dir
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkDirWrite($dir='')
    {
        $route = $this->getAppRoot().'/'.$dir;
        return $result = is_writable($route) ? 'ok' : 'fail';
    }

    /**
     * 检查目录是否可写
     * @param $dir
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function checkSuperiorDirWrite($dir='')
    {
        $route = $this->getAppRoot().'/'.$dir;
        return $result = is_writable($route) ? 'ok' : 'fail';
    }


    /**
     * 初始化管理账号
     * @param $post
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function initAccount($post)
    {
        $time = time();
        $salt = substr(md5($time . $post['admin_user']), 0, 4);//随机4位密码盐

        global $uniqueSalt;
        $uniqueSalt = $salt;

        $password = $this->createPassword($post['admin_password'], $salt);

        // 超级管理员
        $sql = "INSERT INTO `la_admin`(`id`, `root`, `name`, `avatar`, `account`, `password`, `login_time`, `login_ip`, `multipoint_login`, `disable`, `create_time`, `update_time`, `delete_time`) VALUES (1, 1, '{$post['admin_user']}', '', '{$post['admin_user']}', '{$password}','{$time}', '', 1, 0, '{$time}', '{$time}', NULL);";
        // 超级管理员关联部门
        $sql .= "INSERT INTO `la_admin_dept` (`admin_id`, `dept_id`) VALUES (1, 1);";

        return $sql;
    }

    /**
     * 生成密码密文
     * @param $pwd
     * @param $salt
     * @return string
     * @author LZH
     * @date 2025/2/20
     */
    public function createPassword($pwd, $salt)
    {
        return md5($salt . md5($pwd . $salt));
    }

    /**
     * 恢复admin,mobile index文件
     * @return void
     * @author LZH
     * @date 2025/2/20
     */
    public function restoreIndexLock()
    {
        $this->checkIndexFile($this->getAppRoot().'/public/mobile');
        $this->checkIndexFile($this->getAppRoot().'/public/admin');
    }

    public function checkIndexFile($path)
    {
        if(file_exists($path.'/index_lock.html')) {
            // 删除提示文件
            unlink($path.'/index.html');
            // 恢复原入口
            rename($path.'/index_lock.html', $path.'/index.html');
        }
    }

}
