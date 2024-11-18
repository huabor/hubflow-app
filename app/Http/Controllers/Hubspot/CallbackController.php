<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hubspot;

use App\Models\HubspotToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;

final class CallbackController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): RedirectResponse
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

        return to_route('hubspot.token.index')->with('action', 'close-popup');
    }
}
