<?php

namespace App\Http\Controllers\Billing;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class CancelSubscriptionController
{
    /**
     * Update the user's profile information.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->subscription('default')->cancel();

        return to_route('billing.choose-subscription')->with('notification', [
            'type' => 'success',
            'message' => 'Subscription successfully resumed!',
        ]);
    }
}