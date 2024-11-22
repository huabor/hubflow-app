<?php

namespace App\Http\Controllers\App\ContactCluster;

use App\Models\ContactCluster;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AppController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(Request $request, ContactCluster $cluster): JsonResponse
    {
        $user = $request->user();
        $hub = $user->selectedHub;

        Debugbar::startMeasure('render', 'Read all Objects');
        $objects = $cluster->resolvedObjects;
        Debugbar::stopMeasure('render');

        Debugbar::startMeasure('render', 'Send response');

        return response()->json($objects);
        Debugbar::stopMeasure('render');
    }
}
