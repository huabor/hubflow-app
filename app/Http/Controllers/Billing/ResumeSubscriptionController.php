<?php

namespace App\Http\Controllers\Billing;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ResumeSubscriptionController
{
    /**
     * Update the user's profile information.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();
        $hub = $user->selectedHub;

        if (! $hub->subscription('default')->onGracePeriod()) {
            return to_route('billing.choose-subscription')->with('notification', [
                'type' => 'error',
                'message' => 'Subscription cannot be resumed!',
            ]);
        }

        $hub->subscription('default')->resume();

        return to_route('billing.choose-subscription')->with('notification', [
            'type' => 'success',
            'message' => 'Subscription successfully resumed!',
        ]);
    }
}
