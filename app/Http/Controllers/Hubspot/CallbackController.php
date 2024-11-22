<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hubspot;

use App\Enums\AppType;
use App\Events\System\Registered;
use App\Http\Integrations\Hubspot\CrmConnector;
use App\Http\Integrations\Hubspot\Requests\GetOwner;
use App\Models\App;
use App\Models\Hub;
use App\Models\HubspotToken;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

final class CallbackController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        /** @var \SocialiteProviders\HubSpot $provider */
        $provider = Socialite::driver('hubspot');
        $hubspotUser = $provider->stateless()->user();

        $hubspotCrmConnector = new CrmConnector(
            token: $hubspotUser->token,
        );
        $getOwner = new GetOwner(
            userId: $hubspotUser->id,
            idProperty: 'userId'
        );
        $ownerResponse = $hubspotCrmConnector->send($getOwner);
        $owner = $ownerResponse->json();

        $firstname = $owner['firstName'];
        $lastname = $owner['lastName'];

        $user = User::query()->updateOrCreate(
            attributes: [
                'hubspot_id' => $hubspotUser->id,
            ],

            values: [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $hubspotUser->email,
            ]
        );

        $hub = Hub::query()
            ->where(
                column: 'hub_id',
                operator: '=',
                value: $hubspotUser->user['hub_id']
            )
            ->first();

        if ($hub === null) {
            $hub = Hub::query()->create(
                attributes: [
                    'user_id' => $user->id,
                    'hub_id' => $hubspotUser->user['hub_id'],
                    'domain' => $hubspotUser->user['hub_domain'],
                ]
            );

            foreach (AppType::TYPE_DEFINITION as $appType) {
                $app = new App;
                $app->hub_id = $hub->id;
                $app->type = AppType::CONTACT_CLUSTER;
                $app->name = $appType['name'];
                $app->configuration = $appType['configuration'];
                $app->save();
            }
        } elseif ($hub->domain !== $hubspotUser->user['hub_domain']) {
            $hub->domain = $hubspotUser->user['hub_domain'];
            $hub->save();
        }

        HubspotToken::query()->updateOrCreate(
            attributes: [
                'user_id' => $user->id,
                'hub_id' => $hub->id,
                'hubspot_user_id' => $hubspotUser->user['user_id'],
            ],

            values: [
                'token' => $hubspotUser->token,
                'refresh_token' => $hubspotUser->refreshToken,
                'email' => $hubspotUser->user['user'],
            ]
        );

        $user->hub_id = $hub->id;
        $user->save();

        if ($user->wasRecentlyCreated) {
            event(new Registered($user));
        }

        Auth::login($user);

        if ($request->get('state') === 'popup') {
            return to_route('hubspot.token.index')->with('action', 'close-popup');
        } else {
            return to_route('app.index');
        }
    }
}
