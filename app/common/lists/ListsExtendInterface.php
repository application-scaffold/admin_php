<?php
declare(strict_types=1);

namespace app\common\lists;

/**
 * @interface ListsExtendInterface
 * @package app\common\lists
 * @author LZH
 * @date 2025/2/18
 */
interface ListsExtendInterface
{

    /**
     * 扩展字段
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    public function extend(): array;

}