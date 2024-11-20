<?php

namespace App\Http\Controllers\Hubspot;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class IndexController
{
    public function __invoke(Request $request): Response
    {
        $tokens = $request->user()->hubspotTokens;
        $tokens->load('hub');

        return Inertia::render('Hubspot/Index', [
            'tokens' => $tokens,
        ]);
    }
}
