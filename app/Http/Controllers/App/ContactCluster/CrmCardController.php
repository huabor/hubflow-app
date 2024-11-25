<?php

namespace App\Http\Controllers\App\ContactCluster;

use App\Enums\AppType;
use App\Models\App;
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
            ->first();

        $app = $hub->apps()
            ->where(
                column: 'type',
                operator: '=',
                value: AppType::CONTACT_CLUSTER
            )
            ->first();

        if ($app === null) {
            $appType = AppType::TYPE_DEFINITION[AppType::CONTACT_CLUSTER->value];
            $app = new App;
            $app->hub_id = $hub->id;
            $app->type = AppType::CONTACT_CLUSTER;
            $app->name = $appType['name'];
            $app->configuration = $appType['configuration'];
            $app->save();
        }

        $token = $user->createToken(
            name: 'crm-card',
            abilities: ['crm-card'],
            expiresAt: now()->addHours(6)
        );

        $appUrl = route('app.show', [
            'type' => AppType::CONTACT_CLUSTER,
        ]);

        $iframeRouteParameter = [
            'type' => AppType::CONTACT_CLUSTER,
            'token' => $token->plainTextToken,
        ];
        if ($hubspotObject !== null) {
            $iframeRouteParameter['company'] = $hubspotObject->id;
        }

        $iframeUrl = route('crm-card.show', $iframeRouteParameter);

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
                    'title' => $hubspotObject !== null ?
                        "HubFlow Apps - Contact Cluster - $hubspotObject->name" :
                        'HubFlow Apps - Contact Cluster',
                    'description' => $hubspotObject !== null ?
                        "Show $hubspotObject->name and surrounding companies on the world map." :
                        'Show your customers on the world map.',
                    'link' => $appUrl,
                ],
            ],
        ];

        return response()->json($result);
    }
}
