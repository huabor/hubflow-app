<?php

namespace App\Http\Controllers\App;

use App\Enums\AppType;
use App\Enums\Hubspot\ObjectType;
use App\Http\Integrations\Hubspot\CrmConnector;
use App\Http\Integrations\Hubspot\Requests\Property\ReadAllProperties;
use App\Models\App;
use App\Models\HubspotObject;
use App\Models\HubspotToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class ShowController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(Request $request, AppType $type): Response|RedirectResponse
    {
        $crmCard = $request->routeIs('crm-card.show');

        $user = $request->user();
        $hub = $user->selectedHub;

        if ($crmCard) {
            Auth::guard('web')->login($user);
        }

        $appType = AppType::TYPE_DEFINITION[$type->value];

        $app = $hub->apps()
            ->where(
                column: 'type',
                operator: '=',
                value: $type
            )
            ->first();

        if ($app === null) {
            $app = new App;
            $app->hub_id = $hub->id;
            $app->type = $type;
            $app->name = $appType['name'];
            $app->configuration = $appType['configuration'];
            $app->save();
        }

        $renderProps = [
            'crmCard' => $crmCard,
            'app' => $app,
        ];

        $view = match ($app->type) {
            AppType::BIRTHDAY_REMINDER => 'App/BirthdayReminder/App',
            AppType::CONTACT_CLUSTER => 'App/ContactCluster/App',
            default => null,
        };

        if ($app->type === AppType::CONTACT_CLUSTER) {
            $renderProps['object_types'] = ObjectType::ALL;

            $contactCluster = $app->contactCluster()
                ->withCount([
                    'objects',
                    'resolvedObjects',
                ])
                ->get();

            $renderProps['contact_cluster'] = $contactCluster;

            if (isset($request->object)) {
                $object = HubspotObject::query()->find($request->object);

                if ($object !== null) {
                    $renderProps['object'] = $object;
                }
            }
        }

        if ($app->type === AppType::BIRTHDAY_REMINDER) {
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
                objectType: 'contacts',
            );
            $propertyResponse = $hubspotCrmConnector->send($readAllProperties);
            $response = $propertyResponse->collect('results');

            $renderProps['contact_properties'] = $response;
        }

        if ($view === null) {
            return to_route('app.index')->with('notification', [
                'type' => 'warning',
                'message' => 'Please try again!',
            ]);
        }

        return Inertia::render($view, $renderProps);
    }
}
