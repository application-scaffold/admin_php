<?php
declare(strict_types=1);

namespace app\common\service\generator\core;


interface GenerateInterface
{
    public function generate();

    public function fileInfo();
}