<?php

namespace App\Http\Controllers\Billing;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class IndexController
{
    /**
     * Update the user's profile information.
     */
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $hub = $user->selectedHub;

        $orders = $hub->orders()
            ->whereNotNull('mollie_payment_id')
            ->orderBy('processed_at', 'DESC')
            ->get();

        return Inertia::render('Billing/Index', [
            'credit' => $hub->credit('EUR'),
            'orders' => $orders,
        ]);
    }
}
