<?php

namespace App\Http\Controllers\App;

use App\Enums\AppType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class CreateController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(Request $request, AppType $type): Response|RedirectResponse
    {
        $appType = AppType::TYPE_DEFINITION[$type->value];

        if ($appType === null) {
            return to_route('app.index')->with('notification', [
                'type' => 'warning',
                'message' => 'App Type was not found. Please try again!',
            ]);
        }

        $tokens = $request->user()->hubspotTokens;

        return Inertia::render('App/Create', [
            'type' => $appType,
            'tokens' => $tokens,
        ]);
    }
}
