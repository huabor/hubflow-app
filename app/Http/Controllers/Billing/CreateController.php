<?php

namespace App\Http\Controllers\Billing;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Laravel\Cashier\SubscriptionBuilder\RedirectToCheckoutResponse;

final class CreateController
{
    public function __invoke(Request $request, string $plan): RedirectResponse|Response
    {
        $user = $request->user();
        $hub = $user->selectedHub;

        $name = 'default';

        if (! $hub->subscribed($name, $plan)) {
            $result = $hub->newSubscription($name, $plan)->create();

            if (is_a($result, RedirectToCheckoutResponse::class)) {
                return Inertia::location($result->getTargetUrl());
            }

            return back()->with('status', 'Welcome to the '.$plan.' plan');
        }

        return back()->with('status', 'You are already on the '.$plan.' plan');
    }
}
