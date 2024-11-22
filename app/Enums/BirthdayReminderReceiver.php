<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\Enum\EnumToArray;

enum BirthdayReminderReceiver: string
{
    use EnumToArray;

    case CONTACT_OWNER = 'contact_owner';
    case EMAIL_RECEIVER = 'email_receiver';
}
