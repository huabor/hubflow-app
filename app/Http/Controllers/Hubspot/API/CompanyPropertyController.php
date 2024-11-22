<?php

namespace App\Http\Controllers\Hubspot\API;

use App\Http\Integrations\Hubspot\CrmConnector;
use App\Http\Integrations\Hubspot\Requests\Property\ReadAllProperties;
use App\Models\HubspotToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CompanyPropertyController
{
    public function __invoke(Request $request): JsonResponse
    {
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

        $readAllProperties = new ReadAllProperties(
            hubId: $hub->id,
            objectType: 'companies',
        );
        if ($request->has('invalidate_cache')) {
            $readAllProperties->invalidateCache();
        }
        $propertyResponse = $hubspotCrmConnector->send($readAllProperties);
        $response = $propertyResponse->collect('results');

        return response()->json($response);
    }
}
