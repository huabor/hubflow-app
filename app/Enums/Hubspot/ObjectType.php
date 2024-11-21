<?php

declare(strict_types=1);

namespace App\Enums\Hubspot;

use App\Traits\Enum\EnumToArray;

enum ObjectType: int
{
    use EnumToArray;

    case COMPANIES = 1;

    const ALL = [
        [
            'type' => self::COMPANIES->value,
            'name' => 'Companies',
            'slug' => 'companies',
        ],
    ];
}
