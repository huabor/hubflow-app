<?php

namespace App\Http\Controllers\App;

use App\Enums\AppType;
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
        $apps = $request->user()->selectedHub->apps;

        return Inertia::render('App/Index', [
            'apps' => $apps,
            'available_apps' => AppType::TYPE_DEFINITION,
        ]);
    }
}
