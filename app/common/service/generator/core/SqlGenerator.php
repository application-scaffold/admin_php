<?php

declare(strict_types=1);

namespace app\common\service\generator\core;


use app\common\enum\GeneratorEnum;
use think\facade\Db;
use think\helper\Str;

/**
 * sql文件生成器
 * @class SqlGenerator
 * @package app\common\service\generator\core
 * @author LZH
 * @date 2025/2/19
 */
class SqlGenerator extends BaseGenerator implements GenerateInterface
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
            '{MENU_TABLE}',
            '{PARTNER_ID}',
            '{LISTS_NAME}',
            '{PERMS_NAME}',
            '{PATHS_NAME}',
            '{COMPONENT_NAME}',
            '{CREATE_TIME}',
            '{UPDATE_TIME}'
        ];

        // 等待替换的内容
        $waitReplace = [
            $this->getMenuTableNameContent(),
            $this->menuConfig['pid'],
            $this->menuConfig['name'],
            $this->getPermsNameContent(),
            $this->getLowerTableName(),
            $this->getLowerTableName(),
            time(),
            time()
        ];

        $templatePath = $this->getTemplatePath('sql/sql');

        // 替换内容
        $content = $this->replaceFileData($needReplace, $waitReplace, $templatePath);

        $this->setContent($content);
    }


    /**
     * 路由权限内容
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getPermsNameContent(): string
    {
        if (!empty($this->classDir)) {
            return $this->classDir . '.' . Str::lower($this->getTableName());
        }
        return Str::lower($this->getTableName());
    }

    /**
     * 获取菜单表内容
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getMenuTableNameContent(): string
    {
        $tablePrefix = config('database.connections.mysql.prefix');
        return $tablePrefix . 'system_menu';
    }


    /**
     * 是否构建菜单
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public function isBuildMenu(): bool
    {
        return $this->menuConfig['type'] == GeneratorEnum::GEN_AUTO;
    }


    /**
     * 构建菜单
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public function buildMenuHandle(): bool
    {
        if (empty($this->content)) {
            return false;
        }
        $sqls = explode(';', trim($this->content));
        //执行sql
        foreach ($sqls as $sql) {
            if (!empty(trim($sql))) {
                Db::execute($sql . ';');
            }
        }
        return true;
    }

    /**
     * 获取文件生成到模块的文件夹路径
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public function getModuleGenerateDir(): string
    {
        $dir = $this->generatorDir . 'sql/';
        $this->checkDir($dir);
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
        $dir = $this->generatorDir . 'sql/';
        $this->checkDir($dir);
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
        return 'menu.sql';
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
            'type' => 'sql',
            'content' => $this->content
        ];
    }

}