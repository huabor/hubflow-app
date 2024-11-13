<?php

namespace App\Console\Commands;

use App\Http\Integrations\Hubspot\CrmConnector;
use App\Http\Integrations\Hubspot\Requests\GetCompany;
use App\Http\Integrations\Hubspot\Requests\GetCompanyProperty;
use App\Http\Integrations\Hubspot\Requests\GetOwner;
use App\Http\Integrations\Hubspot\Requests\SearchCompanies;
use App\Mail\BirthdayReminder as BirthdayReminderMail;
use App\Models\HubspotCompany;
use Clickbar\Magellan\Data\Geometries\Point;
use Geocoder\Query\GeocodeQuery;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ImportCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubspot-apps:import-company';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read all companies and save them to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting import company command.');

        // Initialize Hubspot CRM connector with the API token from configuration
        $hubspotCrmConnector = new CrmConnector(
            token: config('services.hubspot.token'),
        );

        $this->info('Hubspot CRM Connector initialized.');

        $this->info('Get industry sector property info');
        $getCompanyProperty = new GetCompanyProperty(
            property: 'industry_sector'
        );
        $res = $hubspotCrmConnector->send($getCompanyProperty);
        $industrySectorOptions = $res->collect('options');
        $this->info('Industry sector property info response received.');

        $this->info('Searching for companies.');
        $searchCompanies = new SearchCompanies;
        $res = $hubspotCrmConnector->send($searchCompanies);
        $this->info('Search companies response received.');

        // Collect contact results
        $companies = $res->collect('results');

        foreach ($companies as $company) {
            $companyName = $company['properties']['name'];
            $this->info("Store company $companyName");

            $industrySector = $industrySectorOptions->first(fn ($iso) => $iso['value'] === $company['properties']['industry_sector']);

            $company = HubspotCompany::query()->updateOrCreate(
                attributes: [
                    'hubspot_token_id' => 1,
                    'hubspot_id' => $company['id'],
                ],
                values: [
                    'name' => $company['properties']['name'],
                    'industry_sector' => $industrySector['label'] ?? 'unknown',
                    'address' => $company['properties']['address'],
                    'city' => $company['properties']['city'],
                    'zip' => $company['properties']['zip'],
                    'country' => $company['properties']['country'],
                ]
            );

            $address = "$company->address, $company->city, $company->zip - $company->country";

            $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36';
            $referrer = 'http://localhost';
            $httpClient = new \GuzzleHttp\Client;
            $provider = \Geocoder\Provider\Nominatim\Nominatim::withOpenStreetMapServer($httpClient, $userAgent, $referrer);

            $geocoder = new \Geocoder\StatefulGeocoder($provider, 'de');

            // $result = $geocoder->geocodeQuery(GeocodeQuery::create($address));
            // if (count($result) > 0) {
            //     $company->coordinates = Point::make(
            //         x: $result->first()->getCoordinates()->getLatitude(),
            //         y: $result->first()->getCoordinates()->getLongitude(),
            //     );
            //     $company->save();
            // }
        }
        //     $address = "$company->address, $company->city, $company->zip - $company->country";
        //     echo $address . '<br/>';

        //     $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36';
        //     $referrer = 'http://localhost';
        //     $httpClient = new \GuzzleHttp\Client();
        //     $provider = \Geocoder\Provider\Nominatim\Nominatim::withOpenStreetMapServer($httpClient, $userAgent, $referrer);

        //     $geocoder = new \Geocoder\StatefulGeocoder($provider, 'en');

        //     $result = $geocoder->geocodeQuery(GeocodeQuery::create($address));
        //     if (count($result) > 0) {
        //         $company->coordinates = Point::make(
        //             x: $result->first()->getCoordinates()->getLatitude(),
        //             y: $result->first()->getCoordinates()->getLongitude(),
        //         );
        //         $company->save();
        //     }

        // foreach ($companies as $company) {
        //     $address = "$company->address, $company->city, $company->zip - $company->country";
        //     echo $address . '<br/>';

        //     $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36';
        //     $referrer = 'http://localhost';
        //     $httpClient = new \GuzzleHttp\Client();
        //     $provider = \Geocoder\Provider\Nominatim\Nominatim::withOpenStreetMapServer($httpClient, $userAgent, $referrer);

        //     $geocoder = new \Geocoder\StatefulGeocoder($provider, 'en');

        //     $result = $geocoder->geocodeQuery(GeocodeQuery::create($address));
        //     if (count($result) > 0) {
        //         $company->coordinates = Point::make(
        //             x: $result->first()->getCoordinates()->getLatitude(),
        //             y: $result->first()->getCoordinates()->getLongitude(),
        //         );
        //         $company->save();
        //     }
        // }
        return;
        dd($companies);

        $this->info($notification->name);
        $this->info($contacts->map(fn ($c) => $c['properties']['firstname'].' '.$c['properties']['lastname'].' - '.$c['properties']['date_of_birth'])->join(' | '));

        $owners = collect(); // Cache for owners to avoid multiple API calls
        $companies = collect(); // Cache for companies to avoid multiple API calls

        $birthdays = $contacts->map(function ($contact) use ($hubspotCrmConnector, $owners, $companies) {
            $this->info('Processing contact ID: '.$contact['id']);

            $contactId = $contact['id'];
            $properties = $contact['properties'];

            // Store contact details
            $result = [
                'firstname' => $properties['firstname'],
                'lastname' => $properties['lastname'],
                'dateOfBirth' => $properties['date_of_birth'],
                'birthdayText' => $properties['birthdaytext'],
                'hubspotContactUrl' => "https://app-eu1.hubspot.com/contacts/143411655/record/0-1/{$contactId}",
            ];

            $this->info('Contact name: '.$result['firstname'].' '.$result['lastname']);

            // Fetch owner details, using cache if available
            $ownerId = $properties['hubspot_owner_id'];
            $owner = $owners->get($ownerId);

            if (! $owner) {
                $this->info('Fetching owner ID: '.$ownerId);
                $getOwner = new GetOwner(userId: $ownerId);
                $getOwnerResponse = $hubspotCrmConnector->send($getOwner);
                $owner = $getOwnerResponse->json();
                $owners->put($ownerId, $owner);
                $this->info('Owner data cached for ID: '.$ownerId);
            } else {
                $this->info('Owner ID '.$ownerId.' found in cache.');
            }

            // Store owner details
            $result['ownerFirstname'] = $owner['firstName'];
            $result['ownerLastname'] = $owner['lastName'];
            $result['ownerEmail'] = $owner['email'];

            // Fetch company details, using cache if available
            $companyId = $properties['associatedcompanyid'];
            $company = $companies->get($companyId);

            if (! $company) {
                $this->info('Fetching company ID: '.$companyId);
                $getCompany = new GetCompany(companyId: $companyId);
                $getCompanyResponse = $hubspotCrmConnector->send($getCompany);
                $company = $getCompanyResponse->json();
                $companies->put($companyId, $company);
                $this->info('Company data cached for ID: '.$companyId);
            } else {
                $this->info('Company ID '.$companyId.' found in cache.');
            }

            // Store company details
            $result['companyDomain'] = $company['properties']['domain'];
            $result['companyName'] = $company['properties']['name'];

            $this->info('Contact processed: '.$result['firstname'].' '.$result['lastname']);

            return $result;
        });

        // Group birthdays by the owner's email address
        $groupedBirthdays = $birthdays->groupBy('ownerEmail');
        $this->info('Birthdays grouped by owner email.');

        // Send an email to each owner with their contacts' birthday details
        $groupedBirthdays->each(function ($groupedBirthday) {
            $receiver = [
                'firstname' => $groupedBirthday->first()['ownerFirstname'],
                'lastname' => $groupedBirthday->first()['ownerLastname'],
                'email' => $groupedBirthday->first()['ownerEmail'],
            ];

            $this->info('Sending email to: '.$receiver['email']);

            // Send the birthday reminder email
            Mail::to(
                // users: $receiver['email'],
                users: [
                    'peter.huber@brandnamic.com',
                    'stephan.kohl@brandnamic.com',
                ],
                name: "{$receiver['firstname']} {$receiver['lastname']}"
            )->send(
                new BirthdayReminderMail(
                    receiver: $receiver,
                    birthdays: $groupedBirthday->toArray()
                )
            );

            $this->info('Email sent to: '.$receiver['email']);
        });

        $this->info('Birthday reminder command completed.');
    }
}
