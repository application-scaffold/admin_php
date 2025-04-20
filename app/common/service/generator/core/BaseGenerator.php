<?php

declare(strict_types=1);

namespace app\common\service\generator\core;

use think\helper\Str;
use app\common\enum\GeneratorEnum;

/**
 * 生成器基类
 * @class BaseGenerator
 * @package app\common\service\generator\core
 * @author LZH
 * @date 2025/2/18
 */
abstract class BaseGenerator
{

    /**
     * 模板文件夹
     * @var string
     */
    protected string $templateDir;

    /**
     * 模块名
     * @var string
     */
    protected string $moduleName;

    /**
     * 类目录
     * @var string
     */
    protected string $classDir;

    /**
     * 表信息
     * @var array
     */
    protected array $tableData;

    /**
     * 表字段信息
     * @var array
     */
    protected array $tableColumn;

    /**
     * 文件内容
     * @var string
     */
    protected string $content;

    /**
     * basePath
     * @var string
     */
    protected string $basePath;

    /**
     * rootPath
     * @var string
     */
    protected string $rootPath;

    /**
     * 生成的文件夹
     * @var string
     */
    protected string $generatorDir;

    /**
     * 删除配置
     * @var array
     */
    protected array $deleteConfig;

    /**
     * 菜单配置
     * @var array
     */
    protected array $menuConfig;

    /**
     * 模型关联配置
     * @var array
     */
    protected array $relationConfig;

    /**
     * 树表配置
     * @var array
     */
    protected array $treeConfig;


    public function __construct()
    {
        $this->basePath = base_path();
        $this->rootPath = root_path();
        $this->templateDir = $this->basePath . 'common/service/generator/stub/';
        $this->generatorDir = $this->rootPath . 'runtime/generate/';
        $this->checkDir($this->generatorDir);
    }


    /**
     * 初始化表表数据
     * @param array $tableData
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function initGenerateData(array $tableData): void
    {
        // 设置当前表信息
        $this->setTableData($tableData);
        // 设置模块名
        $this->setModuleName($tableData['module_name']);
        // 设置类目录
        $this->setClassDir($tableData['class_dir'] ?? '');
        // 替换模板变量
        $this->replaceVariables();
    }

    /**
     * 菜单配置
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function setMenuConfig(): void
    {
        $this->menuConfig = [
            'pid' => $this->tableData['menu']['pid'] ?? 0,
            'type' => $this->tableData['menu']['type'] ?? GeneratorEnum::DELETE_TRUE,
            'name' => $this->tableData['menu']['name'] ?? $this->tableData['table_comment']
        ];
    }


    /**
     * 删除配置
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function setDeleteConfig(): void
    {
        $this->deleteConfig = [
            'type' => $this->tableData['delete']['type'] ?? GeneratorEnum::DELETE_TRUE,
            'name' => $this->tableData['delete']['name'] ?? GeneratorEnum::DELETE_NAME,
        ];
    }

    /**
     * 关联模型配置
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function setRelationConfig(): void
    {
        $this->relationConfig = empty($this->tableData['relations']) ? [] : $this->tableData['relations'];
    }


    /**
     * 设置树表配置
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function setTreeConfig(): void
    {
        $this->treeConfig = [
            'tree_id' => $this->tableData['tree']['tree_id'] ?? '',
            'tree_pid' => $this->tableData['tree']['tree_pid'] ?? '',
            'tree_name' => $this->tableData['tree']['tree_name'] ?? '',
        ];
    }


    /**
     * 生成文件到模块或runtime目录
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function generate(): void
    {
        //生成方式  0-压缩包下载 1-生成到模块
        if ($this->tableData['generate_type']) {
            // 生成路径
            $path = $this->getModuleGenerateDir() . $this->getGenerateName();
        } else {
            // 生成到runtime目录
            $path = $this->getRuntimeGenerateDir() . $this->getGenerateName();
        }
        // 写入内容
        file_put_contents($path, $this->content);
    }


    /**
     * 获取文件生成到模块的文件夹路径
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    abstract public function getModuleGenerateDir(): string;


    /**
     * 获取文件生成到runtime的文件夹路径
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    abstract public function getRuntimeGenerateDir(): string;


    /**
     * 替换模板变量
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    abstract public function replaceVariables(): void;


    /**
     * 生成文件名
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    abstract public function getGenerateName(): string;


    /**
     * 文件夹不存在则创建
     * @param string $path
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function checkDir(string $path): void
    {
        !is_dir($path) && mkdir($path, 0755, true);
    }


    /**
     * 设置表信息
     * @param array $tableData
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function setTableData(array $tableData): void
    {
        $this->tableData = !empty($tableData) ? $tableData : [];
        $this->tableColumn = $tableData['table_column'] ?? [];
        // 菜单配置
        $this->setMenuConfig();
        // 删除配置
        $this->setDeleteConfig();
        // 关联模型配置
        $this->setRelationConfig();
        // 设置树表配置
        $this->setTreeConfig();
    }


    /**
     * 设置模块名
     * @param string $moduleName
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function setModuleName(string $moduleName): void
    {
        $this->moduleName = strtolower($moduleName);
    }


    /**
     * 设置类目录
     * @param string $classDir
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function setClassDir(string $classDir): void
    {
        $this->classDir = $classDir;
    }


    /**
     * 设置生成文件内容
     * @param string $content
     * @return void
     * @author LZH
     * @date 2025/2/18
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * 获取模板路径
     * @param string $templateName
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getTemplatePath(string $templateName): string
    {
        return $this->templateDir . $templateName . '.stub';
    }

    /**
     * 小驼峰命名
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getLowerCamelName(): string
    {
        return Str::camel($this->getTableName());
    }


    /**
     * 大驼峰命名
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getUpperCamelName(): string
    {
        return Str::studly($this->getTableName());
    }

    /**
     * 表名小写
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function getLowerTableName(): string
    {
        return Str::lower($this->getTableName());
    }


    /**
     * 获取表名
     * @return mixed
     * @author LZH
     * @date 2025/2/18
     */
    public function getTableName(): mixed
    {
        return get_no_prefix_table_name($this->tableData['table_name']);
    }

    /**
     * 获取表主键
     * @return mixed|string
     * @author LZH
     * @date 2025/2/18
     */
    public function getPkContent(): mixed
    {
        $pk = 'id';
        if (empty($this->tableColumn)) {
            return $pk;
        }

        foreach ($this->tableColumn as $item) {
            if ($item['is_pk']) {
                $pk = $item['column_name'];
            }
        }
        return $pk;
    }

    /**
     * 获取作者信息
     * @return mixed|string
     * @author LZH
     * @date 2025/2/18
     */
    public function getAuthorContent(): mixed
    {
        return empty($this->tableData['author']) ? 'likeadmin' : $this->tableData['author'];
    }


    /**
     * 代码生成备注时间
     * @return false|string
     * @author LZH
     * @date 2025/2/18
     */
    public function getNoteDateContent(): bool|string
    {
        return date('Y/m/d H:i');
    }

    /**
     * 设置空额占位符
     * @param string $content
     * @param string $blankpace
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function setBlankSpace(string $content, string $blankpace): string
    {
        $content = explode(PHP_EOL, $content);
        foreach ($content as $line => $text) {
            $content[$line] = $blankpace . $text;
        }
        return (implode(PHP_EOL, $content));
    }


    /**
     * 替换内容
     * @param string|array $needReplace
     * @param string|array $waitReplace
     * @param string|array $template
     * @return array|bool|string
     * @author LZH
     * @date 2025/2/18
     */
    public function replaceFileData(string|array $needReplace, string|array $waitReplace, string|array $template): array|bool|string
    {
        return str_replace($needReplace, $waitReplace, file_get_contents($template));
    }

    /**
     * 生成方式是否为压缩包
     * @return bool
     * @author LZH
     * @date 2025/2/18
     */
    public function isGenerateTypeZip(): bool
    {
        return $this->tableData['generate_type'] == GeneratorEnum::GENERATE_TYPE_ZIP;
    }

    /**
     * 是否为树表crud
     * @return bool
     * @author LZH
     * @date 2025/2/18
     */
    public function isTreeCrud(): bool
    {
        return $this->tableData['template_type'] == GeneratorEnum::TEMPLATE_TYPE_TREE;
    }

}