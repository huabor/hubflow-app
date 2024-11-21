<?php

namespace App\Http\Controllers\App\ContactCluster;

use App\Enums\AppType;
use App\Models\Hub;
use App\Models\HubspotObject;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CrmCardController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(Request $request): JsonResponse
    {
        // http://localhost/hubspot/crm-card/contact-cluster?userId=65900859&userEmail=peter.huber@brandnamic.com&associatedObjectId=9483117011&associatedObjectType=COMPANY&portalId=143411655
        $hubId = $request->get('portalId');
        $hubspotUserId = $request->get('userId');
        $hubspotObjectId = $request->get('associatedObjectId');

        $user = User::query()
            ->where('hubspot_id', $hubspotUserId)
            ->firstOrFail();

        $hub = Hub::query()
            ->where('hub_id', $hubId)
            ->firstOrFail();

        $hubspotObject = HubspotObject::query()
            ->where('hub_id', $hub->id)
            ->where('hubspot_id', $hubspotObjectId)
            ->firstOrFail();

        $app = $hub->apps()
            ->where('type', AppType::CONTACT_CLUSTER)
            ->firstOrFail();

        $token = $user->createToken(
            name: 'crm-card',
            abilities: ['crm-card'],
            expiresAt: now()->addHours(6)
        );

        $appUrl = route('app.show', [
            'type' => AppType::CONTACT_CLUSTER,
        ]);

        $iframeUrl = route('crm-card.show', [
            'type' => AppType::CONTACT_CLUSTER,
            'token' => $token->plainTextToken,
            'company' => $hubspotObject->id,
        ]);

        $result = [
            'primaryAction' => [
                'type' => 'IFRAME',
                'width' => 1920,
                'height' => 1080,
                'uri' => $iframeUrl,
                'label' => 'Show Contact Cluster',
                'associatedObjectProperties' => [],
            ],

            // 'secondaryActions' => [[
            //     'type' => 'IFRAME',
            //     'width' => 890,
            //     'height' => 748,
            //     'uri' => $iframeUrl,
            //     'label' => 'Show Contact Cluster',
            //     'associatedObjectProperties' => [],
            // ]],

            'results' => [
                [
                    'objectId' => $app->id,
                    'title' => "Contact Cluster - $hubspotObject->name",
                    'description' => "Show $hubspotObject->name and surrounding companies on the world map.",
                    'link' => $appUrl,
                ],
            ],
        ];

        return response()->json($result);
    }
}
