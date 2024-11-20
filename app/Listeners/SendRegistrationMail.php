<?php

namespace App\Listeners;

use App\Events\System\Registered;
use App\Mail\System\Welcome as WelcomeMail;
use Illuminate\Support\Facades\Mail;

class SendRegistrationMail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        Mail::to(
            users: $user->email,
            name: "$user->firstname $user->lastname",
        )->send(
            new WelcomeMail(
                user: $user,
            )
        );
    }
}
