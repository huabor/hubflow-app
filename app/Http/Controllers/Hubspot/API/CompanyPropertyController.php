<?php

namespace App\Http\Controllers\Hubspot\API;

use App\Http\Integrations\Hubspot\CrmConnector;
use App\Http\Integrations\Hubspot\Requests\GetCompanyProperty;
use App\Models\HubspotToken;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

final class CompanyPropertyController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(Request $request, HubspotToken $token): JsonResponse
    {
        Debugbar::startMeasure('render', 'Refresh Token');
        $refreshToken = Socialite::driver('hubspot')
            ->refreshToken($token->refresh_token);
        $token->token = $refreshToken['access_token'];
        $token->save();
        Debugbar::stopMeasure('render');

        // Initialize Hubspot CRM connector with the API token from configuration
        $hubspotCrmConnector = new CrmConnector(
            token: $token->token,
        );

        Debugbar::startMeasure('render', 'Get all company properties');
        $getCompanyProperty = new GetCompanyProperty;
        $res = $hubspotCrmConnector->send($getCompanyProperty);
        Debugbar::stopMeasure('render');

        Debugbar::startMeasure('render', 'Send response');

        return response()->json($res->json());
        Debugbar::stopMeasure('render');
    }
}
