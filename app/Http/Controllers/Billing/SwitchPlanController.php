<?php

namespace App\Http\Controllers\Billing;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class SwitchPlanController
{
    /**
     * Update the user's profile information.
     */
    public function __invoke(Request $request, string $plan): RedirectResponse
    {
        $user = $request->user();
        $hub = $user->selectedHub;

        $hub->subscription('default')->swap($plan);

        return to_route('billing.choose-subscription')->with('notification', [
            'type' => 'success',
            'message' => 'Subscription successfully switched!',
        ]);
    }
}
