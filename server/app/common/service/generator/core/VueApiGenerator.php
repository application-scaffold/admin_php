<?php

declare(strict_types=1);

namespace app\common\service\generator\core;

use think\helper\Str;

/**
 * vue-api生成器
 * @class VueApiGenerator
 * @package app\common\service\generator\core
 * @author LZH
 * @date 2025/2/19
 */
class VueApiGenerator extends BaseGenerator implements GenerateInterface
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
            '{COMMENT}',
            '{UPPER_CAMEL_NAME}',
            '{ROUTE}'
        ];

        // 等待替换的内容
        $waitReplace = [
            $this->getCommentContent(),
            $this->getUpperCamelName(),
            $this->getRouteContent(),
        ];

        $templatePath = $this->getTemplatePath('vue/api');

        // 替换内容
        $content = $this->replaceFileData($needReplace, $waitReplace, $templatePath);

        $this->setContent($content);
    }


    /**
     * 描述
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public function getCommentContent()
    {
        return $this->tableData['table_comment'];
    }


    /**
     * 路由名称
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getRouteContent()
    {
        $content = $this->getTableName();
        if (!empty($this->classDir)) {
            $content = $this->classDir . '.' . $this->getTableName();
        }
        return Str::lower($content);
    }


    /**
     * 获取文件生成到模块的文件夹路径
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getModuleGenerateDir()
    {
        $dir = dirname(app()->getRootPath()) . '/admin/src/api/';
        $this->checkDir($dir);
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
        $dir = $this->generatorDir . 'vue/src/api/';
        $this->checkDir($dir);
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
        return $this->getLowerTableName() . '.ts';
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
            'type' => 'ts',
            'content' => $this->content
        ];
    }

}