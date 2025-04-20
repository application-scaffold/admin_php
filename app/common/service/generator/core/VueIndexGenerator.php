<?php

declare(strict_types=1);

namespace app\common\service\generator\core;

use app\common\enum\GeneratorEnum;


/**
 * vue-index生成器
 * @class VueIndexGenerator
 * @package app\common\service\generator\core
 * @author LZH
 * @date 2025/2/19
 */
class VueIndexGenerator extends BaseGenerator implements GenerateInterface
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
            '{SEARCH_VIEW}',
            '{LISTS_VIEW}',
            '{UPPER_CAMEL_NAME}',
            '{QUERY_PARAMS}',
            '{DICT_DATA}',
            '{PK}',
            '{API_DIR}',
            '{PERMS_ADD}',
            '{PERMS_EDIT}',
            '{PERMS_DELETE}',
            '{SETUP_NAME}'
        ];

        // 等待替换的内容
        $waitReplace = [
            $this->getSearchViewContent(),
            $this->getListsViewContent(),
            $this->getUpperCamelName(),
            $this->getQueryParamsContent(),
            $this->getDictDataContent(),
            $this->getPkContent(),
            $this->getTableName(),
            $this->getPermsContent(),
            $this->getPermsContent('edit'),
            $this->getPermsContent('delete'),
            $this->getLowerCamelName()
        ];

        $templatePath = $this->getTemplatePath('vue/index');

        if ($this->isTreeCrud()) {
            // 插入树表相关
            array_push($needReplace, '{TREE_ID}', '{TREE_PID}');
            array_push($waitReplace, $this->treeConfig['tree_id'], $this->treeConfig['tree_pid']);
            $templatePath = $this->getTemplatePath('vue/index-tree');
        }

        // 替换内容
        $content = $this->replaceFileData($needReplace, $waitReplace, $templatePath);

        $this->setContent($content);
    }


    /**
     * 获取搜索内容
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getSearchViewContent()
    {
        $content = '';
        foreach ($this->tableColumn as $column) {
            if (!$column['is_query'] || $column['is_pk']) {
                continue;
            }

            $needReplace = [
                '{COLUMN_COMMENT}',
                '{COLUMN_NAME}',
                '{DICT_TYPE}',
            ];
            $waitReplace = [
                $column['column_comment'],
                $column['column_name'],
                $column['dict_type'],
            ];

            $searchStubType = $column['view_type'];
            if ($column['view_type'] == 'radio') {
                $searchStubType = 'select';
            }

            $templatePath = $this->getTemplatePath('vue/search_item/' . $searchStubType);
            if (!file_exists($templatePath)) {
                continue;
            }
            $content .= $this->replaceFileData($needReplace, $waitReplace, $templatePath) . PHP_EOL;
        }

        if (!empty($content)) {
            $content = substr($content, 0, -1);
        }

        $content = $this->setBlankSpace($content, '                ');
        return $content;
    }

    /**
     * 获取列表内容
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getListsViewContent()
    {
        $content = '';
        foreach ($this->tableColumn as $column) {
            if (!$column['is_lists']) {
                continue;
            }

            $needReplace = [
                '{COLUMN_COMMENT}',
                '{COLUMN_NAME}',
                '{DICT_TYPE}',
            ];
            $waitReplace = [
                $column['column_comment'],
                $column['column_name'],
                $column['dict_type'],
            ];

            $templatePath = $this->getTemplatePath('vue/table_item/default');
            if ($column['view_type'] == 'imageSelect') {
                $templatePath = $this->getTemplatePath('vue/table_item/image');
            }
            if (in_array($column['view_type'], ['select', 'radio', 'checkbox'])) {
                $templatePath = $this->getTemplatePath('vue/table_item/options');
            }
            if ($column['column_type'] == 'int' && $column['view_type'] == 'datetime') {
                $templatePath = $this->getTemplatePath('vue/table_item/datetime');
            }
            if (!file_exists($templatePath)) {
                continue;
            }

            $content .= $this->replaceFileData($needReplace, $waitReplace, $templatePath) . PHP_EOL;
        }
        if (!empty($content)) {
            $content = substr($content, 0, -1);
        }
        return $this->setBlankSpace($content, '                    ');
    }


    /**
     * 获取查询条件内容
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getQueryParamsContent()
    {
        $content = '';
        $queryDate = false;
        foreach ($this->tableColumn as $column) {
            if (!$column['is_query'] || $column['is_pk']) {
                continue;
            }
            $content .= $column['column_name'] . ": ''," . PHP_EOL;
            if ($column['query_type'] == 'between' && $column['view_type'] == 'datetime') {
                $queryDate = true;
            }
        }
        if ($queryDate) {
            $content .= "start_time: ''," . PHP_EOL;
            $content .= "end_time: ''," . PHP_EOL;
        }
        $content = substr($content, 0, -2);
        return $this->setBlankSpace($content, '    ');
    }


    /**
     * 获取字典数据内容
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getDictDataContent()
    {
        $content = '';
        $isExist = [];
        foreach ($this->tableColumn as $column) {
            if (empty($column['dict_type']) || $column['is_pk']) {
                continue;
            }
            if (in_array($column['dict_type'], $isExist)) {
                continue;
            }
            $content .= $column['dict_type'] .",";
            $isExist[] = $column['dict_type'];
        }
        if (!empty($content)) {
            $content = substr($content, 0, -1);
        }
        return $this->setBlankSpace($content, '');
    }


    /**
     * 权限规则
     * @param $type
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getPermsContent($type = 'add')
    {
        if (!empty($this->classDir)) {
            $classDir = $this->classDir . '.';
        } else {
            $classDir = '';
        }
        return trim($classDir . $this->getLowerTableName() . '/' . $type);
    }


    /**
     * 获取文件生成到模块的文件夹路径
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getModuleGenerateDir()
    {
        $dir = dirname(app()->getRootPath()) . '/admin/src/views/' . $this->getLowerTableName() . '/';
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
        $dir = $this->generatorDir . 'vue/src/views/' . $this->getLowerTableName() . '/';
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
        return 'index.vue';
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
            'type' => 'vue',
            'content' => $this->content
        ];
    }

}