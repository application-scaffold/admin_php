<?php
declare(strict_types=1);

namespace app\common\lists;

/**
 * @interface ListsSortInterface
 * @package app\common\lists
 * @author LZH
 * @date 2025/2/18
 */
interface ListsSortInterface
{

    /**
     * 设置支持排序字段
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public function setSortFields(): array;

    /**
     * 设置默认排序条件
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public function setDefaultOrder():array;

}