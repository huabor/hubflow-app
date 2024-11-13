<?php

namespace App\Http\Controllers\Billing;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Laravel\Cashier\SubscriptionBuilder\RedirectToCheckoutResponse;

final class CreateController
{
    /**
     * Update the user's profile information.
     */
    public function __invoke(Request $request, string $plan): RedirectResponse|Response
    {
        $user = $request->user();

        $name = 'default';

        if (! $user->subscribed($name, $plan)) {
            $result = $user->newSubscription($name, $plan)->create();

            if (is_a($result, RedirectToCheckoutResponse::class)) {
                return Inertia::location($result->getTargetUrl());
            }

            return back()->with('status', 'Welcome to the '.$plan.' plan');
        }

        return back()->with('status', 'You are already on the '.$plan.' plan');
    }
}
