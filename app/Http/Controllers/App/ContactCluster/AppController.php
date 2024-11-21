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

        // Debugbar::startMeasure('render', 'Read all Objects');
        // $objects = DB::table(HubspotObject::getTableName())
        //     ->where(
        //         column: 'hub_id',
        //         operator: '=',
        //         value: $hub->id
        //     )
        //     ->whereNotNull(
        //         columns: 'location'
        //     )
        //     // ->limit(5)
        //     ->get();
        // Debugbar::stopMeasure('render');

        // Debugbar::startMeasure('render', 'Prepare coordinates');
        // $parser = app()->make(WKBParser::class);
        // foreach ($objects as $object) {
        //     $object->properties = json_decode($object->properties);
        //     $object->deep_link = "https://app-eu1.hubspot.com/contacts/{$object->hub_id}/record/0-2/{$object->hubspot_id}";
        //     $location = $parser->parse($object->location);
        //     $object->coordinates = [
        //         'x' => $location->getX(),
        //         'y' => $location->getY(),
        //     ];
        // }
        // Debugbar::stopMeasure('render');

        Debugbar::startMeasure('render', 'Send response');

        return response()->json($objects);
        Debugbar::stopMeasure('render');
    }
}
