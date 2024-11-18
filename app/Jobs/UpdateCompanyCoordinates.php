<?php

namespace App\Jobs;

use App\Models\HubspotCompany;
use Clickbar\Magellan\Data\Geometries\Point;
use Geocoder\Query\GeocodeQuery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateCompanyCoordinates implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected HubspotCompany $company
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $company = $this->company;
        $address = "$company->address, $company->city, $company->zip - $company->country";

        $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36';
        $referrer = 'http://localhost';
        $httpClient = new \GuzzleHttp\Client;
        $provider = \Geocoder\Provider\Nominatim\Nominatim::withOpenStreetMapServer($httpClient, $userAgent, $referrer);
        $geocoder = new \Geocoder\StatefulGeocoder($provider, 'de');

        $result = $geocoder->geocodeQuery(GeocodeQuery::create($address));
        if (count($result) > 0) {
            $company->coordinates = Point::make(
                x: $result->first()->getCoordinates()->getLatitude(),
                y: $result->first()->getCoordinates()->getLongitude(),
            );
            $company->save();
        }
    }
}
