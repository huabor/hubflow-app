<?php

namespace App\Console\Commands;

use App\Http\Integrations\Hubspot\CrmConnector;
use App\Http\Integrations\Hubspot\Requests\SearchCompanies;
use App\Jobs\UpdateCompanyCoordinates;
use App\Models\HubspotCompany;
use App\Models\HubspotToken;
use Illuminate\Console\Command;
use Laravel\Socialite\Facades\Socialite;

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

        $hubspotTokens = HubspotToken::query()
            ->where('user_id', 2)
            ->get();

        foreach ($hubspotTokens as $hubspotToken) {
            $this->info("Import for Token $hubspotToken->email - $hubspotToken->hub_domain");

            $refreshToken = Socialite::driver('hubspot')
                ->refreshToken($hubspotToken->refresh_token);
            $hubspotToken->token = $refreshToken['access_token'];
            $hubspotToken->save();

            // Initialize Hubspot CRM connector with the API token from configuration
            $hubspotCrmConnector = new CrmConnector(
                token: $hubspotToken->token,
            );

            $this->info('Hubspot CRM Connector initialized.');

            $this->info('Searching for companies.');
            $searchCompanies = new SearchCompanies;
            $paginator = $hubspotCrmConnector->paginate($searchCompanies);
            $this->info('Search companies response received.');

            foreach ($paginator->items() as $company) {
                $companyName = $company['properties']['name'];
                $this->info("Store company $companyName");

                $company = HubspotCompany::query()->updateOrCreate(
                    attributes: [
                        'hub_id' => $hubspotToken->hub_id,
                        'hubspot_id' => $company['id'],
                    ],
                    values: [
                        'name' => $company['properties']['name'],
                        'address' => $company['properties']['address'],
                        'city' => $company['properties']['city'],
                        'zip' => $company['properties']['zip'],
                        'country' => $company['properties']['country'],
                    ]
                );

                UpdateCompanyCoordinates::dispatch($company);
            }
        }

        $this->info('Import company command completed.');
    }
}
