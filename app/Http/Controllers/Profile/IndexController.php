<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class IndexController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Profile/Edit');
    }
}
