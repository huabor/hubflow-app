<?php

namespace App\Http\Controllers\Billing;

use Illuminate\Http\Request;
use Laravel\Cashier\Order\Order;
use Symfony\Component\HttpFoundation\Response;

final class DownloadInvoiceController
{
    /**
     * Update the user's profile information.
     */
    public function __invoke(Request $request, Order $order): Response
    {
        return $request->user()->downloadInvoice($order->id);
    }
}
