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

        $orders = $user->orders()
            ->whereNotNull('mollie_payment_id')
            ->orderBy('processed_at', 'DESC')
            ->get();
        // dd($orders,$user->credit('EUR'));

        return Inertia::render('Billing/Index', [
            'credit' => $user->credit('EUR'),
            'orders' => $orders,
        ]);
    }
}
