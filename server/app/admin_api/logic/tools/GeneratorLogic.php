<?php

namespace app\admin_api\logic\tools;

use app\common\enum\GeneratorEnum;
use app\common\logic\BaseLogic;
use app\common\model\tools\GenerateColumn;
use app\common\model\tools\GenerateTable;
use app\common\service\generator\GenerateService;
use think\facade\Db;


/**
 * 生成器逻辑
 * @class GeneratorLogic
 * @package app\admin_api\logic\tools
 * @author LZH
 * @date 2025/2/19
 */
class GeneratorLogic extends BaseLogic
{

    /**
     * 表详情
     * @param $params
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getTableDetail($params): array
    {
        $detail = GenerateTable::with('table_column')
            ->findOrEmpty((int)$params['id'])
            ->toArray();

        $options = self::formatConfigByTableData($detail);
        $detail['menu'] = $options['menu'];
        $detail['delete'] = $options['delete'];
        $detail['tree'] = $options['tree'];
        $detail['relations'] = $options['relations'];
        return $detail;
    }

    /**
     * 选择数据表
     * @param $params
     * @param $adminId
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function selectTable($params, $adminId)
    {
        Db::startTrans();
        try {
            foreach ($params['table'] as $item) {
                // 添加主表基础信息
                $generateTable = self::initTable($item, $adminId);
                // 获取数据表字段信息
                $column = self::getTableColumn($item['name']);
                // 添加表字段信息
                self::initTableColumn($column, $generateTable['id']);
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 编辑表信息
     * @param $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function editTable($params)
    {
        Db::startTrans();
        try {
            // 格式化配置
            $options = self::formatConfigByTableData($params);
            // 更新主表-数据表信息
            GenerateTable::update([
                'id' => $params['id'],
                'table_name' => $params['table_name'],
                'table_comment' => $params['table_comment'],
                'template_type' => $params['template_type'],
                'author' => $params['author'] ?? '',
                'remark' => $params['remark'] ?? '',
                'generate_type' => $params['generate_type'],
                'module_name' => $params['module_name'],
                'class_dir' => $params['class_dir'] ?? '',
                'class_comment' => $params['class_comment'] ?? '',
                'menu' => $options['menu'],
                'delete' => $options['delete'],
                'tree' => $options['tree'],
                'relations' => $options['relations'],
            ]);

            // 更新从表-数据表字段信息
            foreach ($params['table_column'] as $item) {
                GenerateColumn::update([
                    'id' => $item['id'],
                    'column_comment' => $item['column_comment'] ?? '',
                    'is_required' => $item['is_required'] ?? 0,
                    'is_insert' => $item['is_insert'] ?? 0,
                    'is_update' => $item['is_update'] ?? 0,
                    'is_lists' => $item['is_lists'] ?? 0,
                    'is_query' => $item['is_query'] ?? 0,
                    'query_type' => $item['query_type'],
                    'view_type' => $item['view_type'],
                    'dict_type' => $item['dict_type'] ?? '',
                ]);
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 删除表相关信息
     * @param $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function deleteTable($params)
    {
        Db::startTrans();
        try {
            GenerateTable::whereIn('id', $params['id'])->delete();
            GenerateColumn::whereIn('table_id', $params['id'])->delete();
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * 同步表字段
     * @param $params
     * @return bool
     * @author LZH
     * @date 2025/2/19
     */
    public static function syncColumn($params)
    {
        Db::startTrans();
        try {
            // table 信息
            $table = GenerateTable::findOrEmpty($params['id']);
            // 删除旧字段
            GenerateColumn::whereIn('table_id', $table['id'])->delete();
            // 获取当前数据表字段信息
            $column = self::getTableColumn($table['table_name']);
            // 创建新字段数据
            self::initTableColumn($column, $table['id']);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }


    /**
     * 生成代码
     * @param $params
     * @return false|string[]
     * @author LZH
     * @date 2025/2/19
     */
    public static function generate($params)
    {
        try {
            // 获取数据表信息
            $tables = GenerateTable::with(['table_column'])
                ->whereIn('id', $params['id'])
                ->select()->toArray();

            $generator = app()->make(GenerateService::class);
            $generator->delGenerateDirContent();
            $flag = array_unique(array_column($tables, 'table_name'));
            $flag = implode(',', $flag);
            $generator->setGenerateFlag(md5($flag . time()), false);

            // 循环生成
            foreach ($tables as $table) {
                $generator->generate($table);
            }

            $zipFile = '';
            // 生成压缩包
            if ($generator->getGenerateFlag()) {
                $generator->zipFile();
                $generator->delGenerateFlag();
                $zipFile = $generator->getDownloadUrl();
            }

            return ['file' => $zipFile];

        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }


    /**
     * 预览
     * @param $params
     * @return array|false
     * @author LZH
     * @date 2025/2/19
     */
    public static function preview($params)
    {
        try {
            // 获取数据表信息
            $table = GenerateTable::with(['table_column'])
                ->whereIn('id', $params['id'])
                ->findOrEmpty()->toArray();

            return app()->make(GenerateService::class)->preview($table);

        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }


    /**
     * 获取表字段信息
     * @param $tableName
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getTableColumn($tableName)
    {
        $tableName = get_no_prefix_table_name($tableName);
        return Db::name($tableName)->getFields();
    }


    /**
     * 初始化代码生成数据表信息
     * @param $tableData
     * @param $adminId
     * @return GenerateTable|\think\model\contract\Modelable
     * @author LZH
     * @date 2025/2/19
     */
    public static function initTable($tableData, $adminId)
    {
        return GenerateTable::create([
            'table_name' => $tableData['name'],
            'table_comment' => $tableData['comment'],
            'template_type' => GeneratorEnum::TEMPLATE_TYPE_SINGLE,
            'generate_type' => GeneratorEnum::GENERATE_TYPE_ZIP,
            'module_name' => 'admin_api',
            'admin_id' => $adminId,
            // 菜单配置
            'menu' => [
                'pid' => 0, // 父级菜单id
                'type' => GeneratorEnum::GEN_SELF, // 构建方式 0-手动添加 1-自动构建
                'name' => $tableData['comment'], // 菜单名称
            ],
            // 删除配置
            'delete' => [
                'type' => GeneratorEnum::DELETE_TRUE, // 删除类型
                'name' => GeneratorEnum::DELETE_NAME, // 默认删除字段名
            ],
            // 关联配置
            'relations' => [],
            // 树形crud
            'tree' => []
        ]);
    }


    /**
     * 初始化代码生成字段信息
     * @param $column
     * @param $tableId
     * @return void
     * @throws \Exception
     * @author LZH
     * @date 2025/2/19
     */
    public static function initTableColumn($column, $tableId)
    {
        $defaultColumn = ['id', 'create_time', 'update_time', 'delete_time'];

        $insertColumn = [];
        foreach ($column as $value) {
            $required = 0;
            if ($value['notnull'] && !$value['primary'] && !in_array($value['name'], $defaultColumn)) {
                $required = 1;
            }

            $columnData = [
                'table_id' => $tableId,
                'column_name' => $value['name'],
                'column_comment' => $value['comment'],
                'column_type' => self::getDbFieldType($value['type']),
                'is_required' => $required,
                'is_pk' => $value['primary'] ? 1 : 0,
            ];

            if (!in_array($value['name'], $defaultColumn)) {
                $columnData['is_insert'] = 1;
                $columnData['is_update'] = 1;
                $columnData['is_lists'] = 1;
                $columnData['is_query'] = 1;
            }
            $insertColumn[] = $columnData;
        }

        (new GenerateColumn())->saveAll($insertColumn);
    }


    /**
     * 下载文件
     * @param string $fileName
     * @return false|string
     * @author LZH
     * @date 2025/2/19
     */
    public static function download(string $fileName)
    {
        $cacheFileName = cache('curd_file_name' . $fileName);
        if (empty($cacheFileName)) {
            self::$error = '请重新生成代码';
            return false;
        }

        $path = root_path() . 'runtime/generate/' . $fileName;
        if (!file_exists($path)) {
            self::$error = '下载失败';
            return false;
        }

        cache('curd_file_name' . $fileName, null);
        return $path;
    }


    /**
     * 获取数据表字段类型
     * @param string $type
     * @return string
     * @author LZH
     * @date 2025/2/19
     */
    public static function getDbFieldType(string $type): string
    {
        if (0 === strpos($type, 'set') || 0 === strpos($type, 'enum')) {
            $result = 'string';
        } elseif (preg_match('/(double|float|decimal|real|numeric)/is', $type)) {
            $result = 'float';
        } elseif (preg_match('/(int|serial|bit)/is', $type)) {
            $result = 'int';
        } elseif (preg_match('/bool/is', $type)) {
            $result = 'bool';
        } elseif (0 === strpos($type, 'timestamp')) {
            $result = 'timestamp';
        } elseif (0 === strpos($type, 'datetime')) {
            $result = 'datetime';
        } elseif (0 === strpos($type, 'date')) {
            $result = 'date';
        } else {
            $result = 'string';
        }
        return $result;
    }


    /**
     * @param $options
     * @return mixed
     * @author LZH
     * @date 2025/2/19
     */
    public static function formatConfigByTableData($options)
    {
        // 菜单配置
        $menuConfig = $options['menu'] ?? [];
        // 删除配置
        $deleteConfig = $options['delete'] ?? [];
        // 关联配置
        $relationsConfig = $options['relations'] ?? [];
        // 树表crud配置
        $treeConfig = $options['tree'] ?? [];

        $relations = [];
        foreach ($relationsConfig as $relation) {
            $relations[] = [
                'name' => $relation['name'] ?? '',
                'model' => $relation['model'] ?? '',
                'type' => $relation['type'] ?? GeneratorEnum::RELATION_HAS_ONE,
                'local_key' => $relation['local_key'] ?? 'id',
                'foreign_key' => $relation['foreign_key'] ?? 'id',
            ];
        }

        $options['menu'] = [
            'pid' => intval($menuConfig['pid'] ?? 0),
            'type' => intval($menuConfig['type'] ?? GeneratorEnum::GEN_SELF),
            'name' => !empty($menuConfig['name']) ? $menuConfig['name'] : $options['table_comment'],
        ];
        $options['delete'] = [
            'type' => intval($deleteConfig['type'] ?? GeneratorEnum::DELETE_TRUE),
            'name' => !empty($deleteConfig['name']) ? $deleteConfig['name'] : GeneratorEnum::DELETE_NAME,
        ];
        $options['relations'] = $relations;
        $options['tree'] = [
            'tree_id' => $treeConfig['tree_id'] ?? "",
            'tree_pid' =>$treeConfig['tree_pid'] ?? "",
            'tree_name' => $treeConfig['tree_name'] ?? '',
        ];

        return $options;
    }


    /**
     * 获取所有模型
     * @param $module
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public static function getAllModels($module = 'common')
    {
        if(empty($module)) {
            return [];
        }
        $modulePath = base_path() . $module . '/model/';
        if(!is_dir($modulePath)) {
            return [];
        }

        $modulefiles = glob($modulePath . '*');
        $targetFiles = [];
        foreach ($modulefiles as $file) {
            $fileBaseName = basename($file, '.php');
            if (is_dir($file)) {
                $file = glob($file . '/*');
                foreach ($file as $item) {
                    if (is_dir($item)) {
                        continue;
                    }
                    $targetFiles[] = sprintf(
                        "\\app\\" . $module . "\\model\\%s\\%s",
                        $fileBaseName,
                        basename($item, '.php')
                    );
                }
            } else {
                if ($fileBaseName == 'BaseModel') {
                    continue;
                }
                $targetFiles[] = sprintf(
                    "\\app\\" . $module . "\\model\\%s",
                    basename($file, '.php')
                );
            }
        }

        return $targetFiles;
    }

}