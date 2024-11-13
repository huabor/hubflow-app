<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hubspot;

use Laravel\Socialite\Facades\Socialite;

final class RedirectController
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return Socialite::driver('hubspot')
            ->scopes([
                'crm.objects.companies.read',
                'crm.objects.contacts.read',
                'crm.objects.owners.read',
            ])
            ->redirect();
    }
}
