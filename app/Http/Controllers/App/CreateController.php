<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class CreateController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(Request $request, string $type): Response|RedirectResponse
    {
        $appType = collect(config('app_types'))->first(fn ($t) => $t['type'] === $type);
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
