<?php

namespace App\Http\Controllers\Billing;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ChooseSubscriptionController
{
    /**
     * Update the user's profile information.
     */
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Billing/ChooseSubscription');
    }
}
