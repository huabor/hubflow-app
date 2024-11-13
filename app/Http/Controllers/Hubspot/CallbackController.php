<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hubspot;

use App\Models\HubspotToken;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;

final class CallbackController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): Response
    {
        $user = Auth::user();
        $hubspotUser = Socialite::driver('hubspot')->user();

        $token = HubspotToken::query()->updateOrCreate(
            attributes: [
                'user_id' => $user->id,
                'hubspot_user_id' => $hubspotUser->user['user_id'],
                'hub_id' => $hubspotUser->user['hub_id'],
            ],

            values: [
                'token' => $hubspotUser->token,
                'refresh_token' => $hubspotUser->refreshToken,
                'email' => $hubspotUser->user['user'],
                'hub_domain' => $hubspotUser->user['hub_domain'],
            ]
        );

        dd($user, $hubspotUser, $token);
    }
}
