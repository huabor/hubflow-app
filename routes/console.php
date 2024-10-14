<?php

use App\Console\Commands\BirthdayReminder;
use Illuminate\Support\Facades\Schedule;

Schedule::command(BirthdayReminder::class)->dailyAt('06:00');
