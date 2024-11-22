<?php

namespace App\Jobs;

use App\Models\Hub;
use App\Models\HubspotObject;
use Clickbar\Magellan\Data\Geometries\Point;
use Geocoder\Query\GeocodeQuery;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ResolveHubspotObjectCoordinates implements ShouldQueue
{
    use Batchable;
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Hub $hub,
        protected HubspotObject $hubspotObject,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...

            return;
        }

        $resolvedObjectLocations = $this->hub->objects()
            ->whereNotNull('location')
            ->count();

        if ($this->hub->subscription() === null) {
            $limit = 20;
        }

        // if ($resolvedObjectLocations >= $limit) {
        //     $this->fail('Maximum numbers of resolved object locations reached.');
        //     return;
        // }

        $properties = $this->hubspotObject->properties;
        $address = "$properties->address, $properties->city, $properties->zip - $properties->country";

        $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36';
        $referrer = 'http://localhost';
        $httpClient = new \GuzzleHttp\Client;
        $provider = \Geocoder\Provider\Nominatim\Nominatim::withOpenStreetMapServer($httpClient, $userAgent, $referrer);
        $geocoder = new \Geocoder\StatefulGeocoder($provider, 'de');

        $result = $geocoder->geocodeQuery(GeocodeQuery::create($address));
        if (count($result) > 0) {
            $this->hubspotObject->location = Point::make(
                x: $result->first()->getCoordinates()->getLatitude(),
                y: $result->first()->getCoordinates()->getLongitude(),
            );
            $this->hubspotObject->save();
        }
    }
}
