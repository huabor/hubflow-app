<?php

namespace App\Http\Controllers\Profile;

use App\Http\Requests\Profile\UpdateRequest;
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

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }
}
