<?php

namespace app\common\service\generator;


use app\common\service\generator\core\ControllerGenerator;
use app\common\service\generator\core\ListsGenerator;
use app\common\service\generator\core\LogicGenerator;
use app\common\service\generator\core\ModelGenerator;
use app\common\service\generator\core\SqlGenerator;
use app\common\service\generator\core\ValidateGenerator;
use app\common\service\generator\core\VueApiGenerator;
use app\common\service\generator\core\VueEditGenerator;
use app\common\service\generator\core\VueIndexGenerator;


/**
 * 生成器
 * @class GenerateService
 * @package app\common\service\generator
 * @author LZH
 * @date 2025/2/18
 */
class GenerateService
{

    // 标记
    protected $flag;

    // 生成文件路径
    protected $generatePath;

    // runtime目录
    protected $runtimePath;

    // 压缩包名称
    protected $zipTempName;

    // 压缩包临时路径
    protected $zipTempPath;

    public function __construct()
    {
        $this->generatePath = root_path() . 'runtime/generate/';
        $this->runtimePath = root_path() . 'runtime/';
    }

    /**
     * 删除生成文件夹内容
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function delGenerateDirContent()
    {
        // 删除runtime目录制定文件夹
        !is_dir($this->generatePath) && mkdir($this->generatePath, 0755, true);
        del_target_dir($this->generatePath, false);
    }


    /**
     * 设置生成状态
     * @param $name
     * @param $status
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function setGenerateFlag($name, $status = false)
    {
        $this->flag = $name;
        cache($name, (int)$status, 3600);
    }

    /**
     * 获取生成状态标记
     * @return mixed|object|\think\App
     * @author LZH
     * @date 2025/2/18
     */
    public function getGenerateFlag()
    {
        return cache($this->flag);
    }


    /**
     * 删除标记时间
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function delGenerateFlag()
    {
        cache($this->flag, null);
    }


    /**
     * 生成器相关类
     * @return string[]
     * @author LZH
     * @date 2025/2/18
     */
    public function getGeneratorClass()
    {
        return [
            ControllerGenerator::class,
            ListsGenerator::class,
            ModelGenerator::class,
            ValidateGenerator::class,
            LogicGenerator::class,
            VueApiGenerator::class,
            VueIndexGenerator::class,
            VueEditGenerator::class,
            SqlGenerator::class,
        ];
    }

    /**
     * 生成文件
     * @param array $tableData
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function generate(array $tableData)
    {
        foreach ($this->getGeneratorClass() as $item) {
            $generator = app()->make($item);
            $generator->initGenerateData($tableData);
            $generator->generate();
            // 是否为压缩包下载
            if ($generator->isGenerateTypeZip()) {
                $this->setGenerateFlag($this->flag, true);
            }
            // 是否构建菜单
            if ($item == 'app\common\service\generator\core\SqlGenerator') {
                $generator->isBuildMenu() && $generator->buildMenuHandle();
            }
        }
    }

    /**
     * 预览文件
     * @param array $tableData
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public function preview(array $tableData)
    {
        $data = [];
        foreach ($this->getGeneratorClass() as $item) {
            $generator = app()->make($item);
            $generator->initGenerateData($tableData);
            $data[] = $generator->fileInfo();
        }
        return $data;
    }


    /**
     * 压缩文件
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function zipFile()
    {
        $fileName = 'curd-' . date('YmdHis') . '.zip';
        $this->zipTempName = $fileName;
        $this->zipTempPath = $this->generatePath . $fileName;
        $zip = new \ZipArchive();
        $zip->open($this->zipTempPath, \ZipArchive::CREATE);
        $this->addFileZip($this->runtimePath, 'generate', $zip);
        $zip->close();
    }

    /**
     * 往压缩包写入文件
     * @param $basePath
     * @param $dirName
     * @param $zip
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function addFileZip($basePath, $dirName, $zip)
    {
        $handler = opendir($basePath . $dirName);
        while (($filename = readdir($handler)) !== false) {
            if ($filename != '.' && $filename != '..') {
                if (is_dir($basePath . $dirName . '/' . $filename)) {
                    // 当前路径是文件夹
                    $this->addFileZip($basePath, $dirName . '/' . $filename, $zip);
                } else {
                    // 写入文件到压缩包
                    $zip->addFile($basePath . $dirName . '/' . $filename, $dirName . '/' . $filename);
                }
            }
        }
        closedir($handler);
    }


    /**
     * 返回压缩包临时路径
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getDownloadUrl()
    {
        $vars = ['file' => $this->zipTempName];
        cache('curd_file_name' . $this->zipTempName, $this->zipTempName, 3600);
        return (string)url("admin_api/tools.generator/download", $vars, false, true);
    }

}