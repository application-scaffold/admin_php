<?php
declare(strict_types=1);

namespace app\admin_api\lists\file;

use app\admin_api\lists\BaseAdminDataLists;
use app\admin_api\logic\FileLogic;
use app\common\enum\FileEnum;
use app\common\lists\ListsSearchInterface;
use app\common\model\file\File;
use app\common\model\file\FileCate;
use app\common\service\FileService;

/**
 * 文件列表
 * @class FileLists
 * @package app\admin_api\lists\file
 * @author LZH
 * @date 2025/2/19
 */
class FileLists extends BaseAdminDataLists implements ListsSearchInterface
{

    /**
     * 文件搜索条件
     * @return array[]
     * @author LZH
     * @date 2025/2/19
     */
    public function setSearch(): array
    {
        return [
            '=' => ['type', 'source'],
            '%like%' => ['name']
        ];
    }

    /**
     * 额外查询处理
     * @return array
     * @author LZH
     * @date 2025/2/19
     */
    public function queryWhere(): array
    {
        $where = [];

        if (!empty($this->params['cid'])) {
            $cateChild = FileLogic::getCateIds($this->params['cid']);
            array_push($cateChild, $this->params['cid']);
            $where[] = ['cid', 'in', $cateChild];
        }

        return $where;
    }


    /**
     * 获取文件列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author LZH
     * @date 2025/2/19
     */
    public function lists(): array
    {
        $lists = (new File())->field(['id,cid,type,name,uri,create_time'])
            ->order('id', 'desc')
            ->where($this->searchWhere)
            ->where($this->queryWhere())
//            ->where('source', FileEnum::SOURCE_ADMIN)
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['url'] = FileService::getFileUrl($item['uri']);
        }

        return $lists;
    }


    /**
     * 获取文件数量
     * @return int
     * @author LZH
     * @date 2025/2/19
     */
    public function count(): int
    {
        return (new File())->where($this->searchWhere)
            ->where($this->queryWhere())
//            ->where('source', FileEnum::SOURCE_ADMIN)
            ->count();
    }
}
