<?php

namespace App\Http\Controllers\Billing;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class UpdatePaymentMethodController
{
    /**
     * Update the user's profile information.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();
        $hub = $user->selectedHub;

        $response = $hub->updatePaymentMethod()->create();

        return $response;
    }
}
