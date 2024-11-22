<?php

namespace App\DTO;

use App\Enums\BirthdayReminderReceiver;
use Spatie\LaravelData\Data;

class BirthdayReminderConfiguration extends Data
{
    public function __construct(
        public bool $enabled,
        public BirthdayReminderReceiver $receiver,
        public string|null $receiver_emails = '',
        public int $send_reminder_before,
        public ?array $properties = []
    ) {
        if ($this->receiver_emails === null) {
            $this->receiver_emails = '';
        }

        if ($this->properties === null) {
            $this->properties = '';
        }
    }
}
