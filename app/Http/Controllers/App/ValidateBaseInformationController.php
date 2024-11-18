<?php

namespace App\Http\Controllers\App;

use App\Http\Requests\App\ValidateBaseInformationRequest;
use Illuminate\Http\RedirectResponse;

final class ValidateBaseInformationController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(ValidateBaseInformationRequest $request): RedirectResponse
    {
        return back();

        return response()->json([
            'success' => true,
        ]);
    }
}
