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
        $request->user()->fill($request->validated());
        $request->user()->save();

        return Redirect::route('profile.edit');
    }
}
