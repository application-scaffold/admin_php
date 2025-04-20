<?php

declare(strict_types=1);

namespace app\common\service\generator\core;


/**
 * 模型生成器
 * @class ModelGenerator
 * @package app\common\service\generator\core
 * @author LZH
 * @date 2025/2/19
 */
class ModelGenerator extends BaseGenerator implements GenerateInterface
{

    /**
     * 替换变量
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public function replaceVariables(): void
    {
        // 需要替换的变量
        $needReplace = [
            '{NAMESPACE}',
            '{CLASS_COMMENT}',
            '{UPPER_CAMEL_NAME}',
            '{PACKAGE_NAME}',
            '{TABLE_NAME}',
            '{USE}',
            '{DELETE_USE}',
            '{DELETE_TIME}',
            '{RELATION_MODEL}',
        ];

        // 等待替换的内容
        $waitReplace = [
            $this->getNameSpaceContent(),
            $this->getClassCommentContent(),
            $this->getUpperCamelName(),
            $this->getPackageNameContent(),
            $this->getTableName(),
            $this->getUseContent(),
            $this->getDeleteUseContent(),
            $this->getDeleteTimeContent(),
            $this->getRelationModel(),
        ];

        $templatePath = $this->getTemplatePath('php/model');

        // 替换内容
        $content = $this->replaceFileData($needReplace, $waitReplace, $templatePath);

        $this->setContent($content);
    }


    /**
     * 获取命名空间模板内容
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getNameSpaceContent(): string
    {
        if (!empty($this->classDir)) {
            return "namespace app\\common\\model\\" . $this->classDir . ';';
        }
        return "namespace app\\common\\model;";
    }


    /**
     * 获取类描述
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getClassCommentContent(): string
    {
        if (!empty($this->tableData['class_comment'])) {
            $tpl = $this->tableData['class_comment'] . '模型';
        } else {
            $tpl = $this->getUpperCamelName() . '模型';
        }
        return $tpl;
    }


    /**
     * 获取包名
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getPackageNameContent(): string
    {
        return !empty($this->classDir) ? '\\' . $this->classDir : '';
    }


    /**
     * 引用内容
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getUseContent(): string
    {
        $tpl = "";
        if ($this->deleteConfig['type']) {
            $tpl = "use think\\model\\concern\\SoftDelete;";
        }
        return $tpl;
    }


    /**
     * 软删除引用
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getDeleteUseContent(): string
    {
        $tpl = "";
        if ($this->deleteConfig['type']) {
            $tpl = "use SoftDelete;";
        }
        return $tpl;
    }


    /**
     * 软删除时间字段定义
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getDeleteTimeContent(): string
    {
        $tpl = "";
        if ($this->deleteConfig['type']) {
            $deleteTime = $this->deleteConfig['name'];
            $tpl = 'protected $deleteTime = ' . "'". $deleteTime ."';";
        }
        return $tpl;
    }


    /**
     * 关联模型
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getRelationModel(): string
    {
        $tpl = '';
        if (empty($this->relationConfig)) {
            return $tpl;
        }

        // 遍历关联配置
        foreach ($this->relationConfig as $config) {
            if (empty($config) || empty($config['name']) || empty($config['model'])) {
                continue;
            }

            $needReplace = [
                '{RELATION_NAME}',
                '{AUTHOR}',
                '{DATE}',
                '{RELATION_MODEL}',
                '{FOREIGN_KEY}',
                '{LOCAL_KEY}',
            ];

            $waitReplace = [
                $config['name'],
                $this->getAuthorContent(),
                $this->getNoteDateContent(),
                $config['model'],
                $config['foreign_key'],
                $config['local_key'],
            ];

            $templatePath = $this->getTemplatePath('php/model/' . $config['type']);
            if (!file_exists($templatePath)) {
                continue;
            }
            $tpl .= $this->replaceFileData($needReplace, $waitReplace, $templatePath) . PHP_EOL;
        }

        return $tpl;
    }


    /**
     * 获取文件生成到模块的文件夹路径
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getModuleGenerateDir(): string
    {
        $dir = $this->basePath . 'common/model/';
        if (!empty($this->classDir)) {
            $dir .= $this->classDir . '/';
            $this->checkDir($dir);
        }
        return $dir;
    }


    /**
     * 获取文件生成到runtime的文件夹路径
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getRuntimeGenerateDir(): string
    {
        $dir = $this->generatorDir . 'php/app/common/model/';
        $this->checkDir($dir);
        if (!empty($this->classDir)) {
            $dir .= $this->classDir . '/';
            $this->checkDir($dir);
        }
        return $dir;
    }


    /**
     * 生成的文件名
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getGenerateName(): string
    {
        return $this->getUpperCamelName() . '.php';
    }


    /**
     * 文件信息
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function fileInfo(): array
    {
        return [
            'name' => $this->getGenerateName(),
            'type' => 'php',
            'content' => $this->content
        ];
    }

}