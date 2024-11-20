<?php

namespace App\Http\Controllers\App\ContactCluster;

use App\Models\App;
use App\Models\HubspotCompany;
use Barryvdh\Debugbar\Facades\Debugbar;
use Clickbar\Magellan\IO\Parser\WKB\WKBParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class ConfigurationController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(Request $request, App $app): JsonResponse|string
    {
        $hubspotToken = $app->hubspotToken;

        Debugbar::startMeasure('render', 'Read all Companies');
        $companies = DB::table(HubspotCompany::getTableName())
            ->where('hubspot_token_id', $hubspotToken->id)
            ->whereNotNull('location')
            // ->limit(5)
            ->get();
        Debugbar::stopMeasure('render');

        Debugbar::startMeasure('render', 'Prepare coordinates');
        $parser = app()->make(WKBParser::class);
        foreach ($companies as $company) {
            $company->deep_link = "https://app-eu1.hubspot.com/contacts/{$company->hub_id}/record/0-2/{$company->hubspot_id}";
            $company->location = $parser->parse($company->location);
        }
        Debugbar::stopMeasure('render');

        Debugbar::startMeasure('render', 'Send response');

        return response()->json($companies);
        Debugbar::stopMeasure('render');
    }
}
