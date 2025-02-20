<?php

namespace app\common\lists;

/**
 * @interface ListsInterface
 * @package app\common\lists
 * @author LZH
 * @date 2025/2/18
 */
interface ListsInterface
{
    /**
     * 实现数据列表
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public function lists(): array;

    /**
     * 实现数据列表记录数
     * @return int
     * @author LZH
     * @date 2025/2/18
     */
    public function count(): int;

}