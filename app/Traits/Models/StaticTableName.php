<?php

declare(strict_types=1);

namespace App\Traits\Models;

trait StaticTableName
{
    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
