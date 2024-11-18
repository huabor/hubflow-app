<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\Enum\EnumToArray;

enum AppType: string
{
    use EnumToArray;

    case CONTACT_CLUSTER = 'contact_cluster';
    case BIRTHDAY_REMINDER = 'birthday_reminder';
}
