<?php

namespace app\common\lists;

/**
 * @interface ListsExcelInterface
 * @package app\common\lists
 * @author LZH
 * @date 2025/2/18
 */
interface ListsExcelInterface
{

    /**
     * 设置导出字段
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public function setExcelFields(): array;


    /**
     * 设置导出文件名
     * @return string
     * @author LZH
     * @date 2025/2/18
     */
    public function setFileName():string;

}