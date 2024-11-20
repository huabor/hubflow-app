<?php

namespace App\Http\Controllers\Billing;

use App\Http\Requests\Billing\UpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

final class UpdateController
{
    /**
     * Update the user's profile information.
     */
    public function __invoke(UpdateRequest $request): RedirectResponse
    {
        $hub = $request->user()->selectedHub;
        $hub->fill($request->validated());
        $hub->save();

        return Redirect::route('billing.index');
    }
}
