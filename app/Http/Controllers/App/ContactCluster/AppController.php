<?php

namespace App\Http\Controllers\App\ContactCluster;

use App\Models\App;
use App\Models\ContactCluster;
use App\Models\HubspotObject;
use Barryvdh\Debugbar\Facades\Debugbar;
use Clickbar\Magellan\IO\Parser\WKB\WKBParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
