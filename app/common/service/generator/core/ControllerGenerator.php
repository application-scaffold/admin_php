<?php

declare(strict_types=1);

namespace app\common\service\generator\core;

/**
 * 控制器生成器
 * @class ControllerGenerator
 * @package app\common\service\generator\core
 * @author LZH
 * @date 2025/2/19
 */
class ControllerGenerator extends BaseGenerator implements GenerateInterface
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
            '{USE}',
            '{CLASS_COMMENT}',
            '{UPPER_CAMEL_NAME}',
            '{MODULE_NAME}',
            '{PACKAGE_NAME}',
            '{EXTENDS_CONTROLLER}',
            '{NOTES}',
            '{AUTHOR}',
            '{DATE}'
        ];

        // 等待替换的内容
        $waitReplace = [
            $this->getNameSpaceContent(),
            $this->getUseContent(),
            $this->getClassCommentContent(),
            $this->getUpperCamelName(),
            $this->moduleName,
            $this->getPackageNameContent(),
            $this->getExtendsControllerContent(),
            $this->tableData['class_comment'],
            $this->getAuthorContent(),
            $this->getNoteDateContent(),
        ];

        $templatePath = $this->getTemplatePath('php/controller');

        // 替换内容
        $content = $this->replaceFileData($needReplace, $waitReplace, $templatePath);

        $this->setContent($content);
    }


    /**
     * 获取命名空间内容
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getNameSpaceContent(): string
    {
        if (!empty($this->classDir)) {
            return "namespace app\\" . $this->moduleName . "\\controller\\" . $this->classDir . ';';
        }
        return "namespace app\\" . $this->moduleName . "\\controller;";
    }


    /**
     * 获取use模板内容
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getUseContent(): string
    {
        if ($this->moduleName == 'admin_api') {
            $tpl = "use app\\" . $this->moduleName . "\\controller\\BaseAdminController;" . PHP_EOL;
        } else {
            $tpl = "use app\\common\\controller\\BaseLikeAdminController;" . PHP_EOL;
        }

        if (!empty($this->classDir)) {
            $tpl .= "use app\\" . $this->moduleName . "\\lists\\" . $this->classDir . "\\" . $this->getUpperCamelName() . "Lists;" . PHP_EOL .
                "use app\\" . $this->moduleName . "\\logic\\" . $this->classDir . "\\" . $this->getUpperCamelName() . "Logic;" . PHP_EOL .
                "use app\\" . $this->moduleName . "\\validate\\" . $this->classDir . "\\" . $this->getUpperCamelName() . "Validate;";
        } else {
            $tpl .= "use app\\" . $this->moduleName . "\\lists\\" . $this->getUpperCamelName() . "Lists;" . PHP_EOL .
                "use app\\" . $this->moduleName . "\\logic\\" . $this->getUpperCamelName() . "Logic;" . PHP_EOL .
                "use app\\" . $this->moduleName . "\\validate\\" . $this->getUpperCamelName() . "Validate;";
        }

        return $tpl;
    }

    /**
     * 获取类描述内容
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getClassCommentContent(): string
    {
        if (!empty($this->tableData['class_comment'])) {
            $tpl = $this->tableData['class_comment'] . '控制器';
        } else {
            $tpl = $this->getUpperCamelName() . '控制器';
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
     * 获取继承控制器
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getExtendsControllerContent(): string
    {
        $tpl = 'BaseAdminController';
        if ($this->moduleName != 'admin_api') {
            $tpl = 'BaseLikeAdminController';
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
        $dir = $this->basePath . $this->moduleName . '/controller/';
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
        $dir = $this->generatorDir . 'php/app/' . $this->moduleName . '/controller/';
        $this->checkDir($dir);
        if (!empty($this->classDir)) {
            $dir .= $this->classDir . '/';
            $this->checkDir($dir);
        }
        return $dir;
    }

    /**
     * 生成文件名
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getGenerateName(): string
    {
        return $this->getUpperCamelName() . 'Controller.php';
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