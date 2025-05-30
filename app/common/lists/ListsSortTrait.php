<?php
declare(strict_types=1);

namespace app\common\lists;

/**
 * @trait ListsSortTrait
 * @package app\common\lists
 * @author LZH
 * @date 2025/2/18
 */
trait ListsSortTrait
{

    protected string $orderBy;
    protected string $field;

    /**
     * 生成排序条件
     * @param array $sortField
     * @param array $defaultOrder
     * @return array
     * @author LZH
     * @date 2025/2/18
     */
    private function createOrder(array $sortField, array $defaultOrder): array
    {
        if (empty($sortField) || empty($this->orderBy) || empty($this->field) || !in_array($this->field, array_keys($sortField))) {
            return $defaultOrder;
        }

        if (isset($sortField[$this->field])) {
            $field = $sortField[$this->field];
        } else {
            return $defaultOrder;
        }

        if ($this->orderBy == 'desc') {
            return [$field => 'desc'];
        }
        if ($this->orderBy == 'asc') {
            return [$field => 'asc'];
        }

        return $defaultOrder;
    }
}