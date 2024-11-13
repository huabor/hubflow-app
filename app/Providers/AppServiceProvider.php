<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\HubSpot\Provider as HubspotProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        if (request()->getHost() !== 'localhost') {
            URL::forceScheme('https');
        }

        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('hubspot', HubspotProvider::class);
        });
    }
}
