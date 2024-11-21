<?php

namespace App\Http\Controllers\Hubspot\API;

use App\Http\Integrations\Hubspot\CrmConnector;
use App\Http\Integrations\Hubspot\Requests\SearchCompanies;
use App\Http\Requests\Hubspot\SearchFilterRequest;
use App\Models\HubspotToken;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

final class CompanySearchController
{
    public function __invoke(SearchFilterRequest $request): JsonResponse|RedirectResponse
    {
        $filter = $request->validated('filter');

        $user = $request->user();
        $hub = $user->selectedHub;

        $token = HubspotToken::query()
            ->where(
                column: 'user_id',
                operator: '=',
                value: $user->id
            )
            ->where(
                column: 'hub_id',
                operator: '=',
                value: $hub->id
            )
            ->firstOrFail();

        // Initialize Hubspot CRM connector with the API token from configuration
        $hubspotCrmConnector = new CrmConnector(
            token: $token->token,
            hubspotToken: $token,
        );

        Debugbar::startMeasure('render', 'Get all company properties');
        $searchCompanies = new SearchCompanies(
            filter: $filter,
        );
        $res = $hubspotCrmConnector->send($searchCompanies);

        $response = [];
        if ($res->status() === 200) {
            $response = [
                'count' => $res->json('total'),
            ];
        }
        Debugbar::stopMeasure('render');

        Debugbar::startMeasure('render', 'Send response');

        return back()->with([
            'data' => $response,
        ]);

        return response()->json($response);
        Debugbar::stopMeasure('render');
    }
}
