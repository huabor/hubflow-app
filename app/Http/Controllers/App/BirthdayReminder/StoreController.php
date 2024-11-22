<?php

namespace App\Http\Controllers\App\BirthdayReminder;

use App\DTO\BirthdayReminderConfiguration;
use App\Http\Requests\App\BirthdayReminder\StoreRequest;
use App\Models\App;
use App\Models\ContactCluster;
use Illuminate\Http\RedirectResponse;

final class StoreController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(StoreRequest $request, App $app): RedirectResponse
    {
        $app->configuration = BirthdayReminderConfiguration::from($request->validated());
        $app->save();

        return to_route('app.show', $app->type)->with('notification', [
            'type' => 'success',
            'message' => "$app->name updated successfully!",
        ]);
    }
}
