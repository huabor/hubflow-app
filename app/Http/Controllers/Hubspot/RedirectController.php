<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hubspot;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

final class RedirectController
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        /** @var \SocialiteProviders\HubSpot $provider */
        $provider = Socialite::driver('hubspot');

        $provider->scopes([
            'crm.objects.companies.read',
            'crm.objects.contacts.read',
            'crm.objects.owners.read',
        ]);

        if ($request->has('state')) {
            $provider->with(['state' => $request->get('state')]);
        }

        return $provider->redirect();
    }
}
