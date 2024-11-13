<?php

use App\Console\Commands\BirthdayReminder;
use Illuminate\Support\Facades\Schedule;
use Laravel\Cashier\Console\Commands\CashierRun;

Schedule::command(BirthdayReminder::class)->dailyAt('06:00');

Schedule::command(CashierRun::class)
    ->hourly() // run as often as you like (daily, monthly, every minute, ...)
    ->withoutOverlapping(); // make sure to include this
