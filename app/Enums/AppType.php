<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\Enum\EnumToArray;

enum AppType: string
{
    use EnumToArray;

    case CONTACT_CLUSTER = 'contact_cluster';
    case BIRTHDAY_REMINDER = 'birthday_reminder';

    const TYPE_DEFINITION = [
        self::CONTACT_CLUSTER->value => [
            'type' => self::CONTACT_CLUSTER->value,
            'name' => 'Contact Cluster',
            'description' => '',
            'configuration' => [],
        ],

        self::BIRTHDAY_REMINDER->value => [
            'type' => self::BIRTHDAY_REMINDER->value,
            'name' => 'Birthday Reminder',
            'description' => '',
            'configuration' => [
                'enabled' => false,
                'send_reminder_before' => 0,
                'receiver' => BirthdayReminderReceiver::CONTACT_OWNER->value,
                'receiver_emails' => '',
            ],
        ],
    ];
}
