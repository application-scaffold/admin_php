<?php

declare(strict_types=1);

namespace app\common\service\generator\core;


/**
 * 验证器生成器
 * @class ValidateGenerator
 * @package app\common\service\generator\core
 * @author LZH
 * @date 2025/2/19
 */
class ValidateGenerator extends BaseGenerator implements GenerateInterface
{

    /**
     * 替换变量
     * @return void
     * @author LZH
     * @date 2025/2/19
     */
    public function replaceVariables()
    {
        // 需要替换的变量
        $needReplace = [
            '{NAMESPACE}',
            '{CLASS_COMMENT}',
            '{UPPER_CAMEL_NAME}',
            '{MODULE_NAME}',
            '{PACKAGE_NAME}',
            '{PK}',
            '{RULE}',
            '{NOTES}',
            '{AUTHOR}',
            '{DATE}',
            '{ADD_PARAMS}',
            '{EDIT_PARAMS}',
            '{FIELD}',
        ];

        // 等待替换的内容
        $waitReplace = [
            $this->getNameSpaceContent(),
            $this->getClassCommentContent(),
            $this->getUpperCamelName(),
            $this->moduleName,
            $this->getPackageNameContent(),
            $this->getPkContent(),
            $this->getRuleContent(),
            $this->tableData['class_comment'],
            $this->getAuthorContent(),
            $this->getNoteDateContent(),
            $this->getAddParamsContent(),
            $this->getEditParamsContent(),
            $this->getFiledContent(),
        ];

        $templatePath = $this->getTemplatePath('php/validate');

        // 替换内容
        $content = $this->replaceFileData($needReplace, $waitReplace, $templatePath);

        $this->setContent($content);
    }


    /**
     * 验证规则
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getRuleContent()
    {
        $content = "'" . $this->getPkContent() . "' => 'require'," . PHP_EOL;
        foreach ($this->tableColumn as $column) {
            if ($column['is_required'] == 1) {
                $content .= "'" . $column['column_name'] . "' => 'require'," . PHP_EOL;
            }
        }
        $content = substr($content, 0, -1);
        return $this->setBlankSpace($content, "        ");
    }


    /**
     * 添加场景验证参数
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getAddParamsContent()
    {
        $content = "";
        foreach ($this->tableColumn as $column) {
            if ($column['is_required'] == 1 && $column['column_name'] != $this->getPkContent()) {
                $content .= "'" . $column['column_name'] . "',";
            }
        }
        $content = substr($content, 0, -1);

        // 若无设置添加场景校验字段时, 排除主键
        if (!empty($content)) {
            $content = 'return $this->only([' . $content . ']);';
        } else {
            $content = 'return $this->remove(' . "'". $this->getPkContent() . "'" . ', true);';
        }

        return $this->setBlankSpace($content, "");
    }


    /**
     * 编辑场景验证参数
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getEditParamsContent()
    {
        $content = "'" . $this->getPkContent() . "'," ;
        foreach ($this->tableColumn as $column) {
            if ($column['is_required'] == 1) {
                $content .= "'" . $column['column_name'] . "',";
            }
        }
        $content = substr($content, 0, -1);
        if (!empty($content)) {
            $content = 'return $this->only([' . $content . ']);';
        }
        return $this->setBlankSpace($content, "");
    }


    /**
     * 验证字段描述
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getFiledContent()
    {
        $content = "'" . $this->getPkContent() . "' => '" . $this->getPkContent() . "'," . PHP_EOL;
        foreach ($this->tableColumn as $column) {
            if ($column['is_required'] == 1) {
                $columnComment = $column['column_comment'];
                if (empty($column['column_comment'])) {
                    $columnComment = $column['column_name'];
                }
                $content .= "'" . $column['column_name'] . "' => '" . $columnComment . "'," . PHP_EOL;
            }
        }
        $content = substr($content, 0, -1);
        return $this->setBlankSpace($content, "        ");
    }


    /**
     * 获取命名空间模板内容
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getNameSpaceContent()
    {
        if (!empty($this->classDir)) {
            return "namespace app\\" . $this->moduleName . "\\validate\\" . $this->classDir . ';';
        }
        return "namespace app\\" . $this->moduleName . "\\validate;";
    }


    /**
     * 获取类描述
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getClassCommentContent()
    {
        if (!empty($this->tableData['class_comment'])) {
            $tpl = $this->tableData['class_comment'] . '验证器';
        } else {
            $tpl = $this->getUpperCamelName() . '验证器';
        }
        return $tpl;
    }


    /**
     * 获取包名
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getPackageNameContent()
    {
        return !empty($this->classDir) ? '\\' . $this->classDir : '';
    }


    /**
     * 获取文件生成到模块的文件夹路径
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getModuleGenerateDir()
    {
        $dir = $this->basePath . $this->moduleName . '/validate/';
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
    public function getRuntimeGenerateDir()
    {
        $dir = $this->generatorDir . 'php/app/' . $this->moduleName . '/validate/';
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
    public function getGenerateName()
    {
        return $this->getUpperCamelName() . 'Validate.php';
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