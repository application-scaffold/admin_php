<?php

namespace app\common\lists;

/**
 * @interface ListsSearchInterface
 * @package app\common\lists
 * @author LZH
 * @date 2025/2/18
 */
interface ListsSearchInterface
{
    /**
     * 设置搜索条件
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public function setSearch(): array;
}