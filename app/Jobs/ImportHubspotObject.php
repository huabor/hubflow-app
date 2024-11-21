<?php

namespace App\Jobs;

use App\Http\Integrations\Hubspot\CrmConnector;
use App\Http\Integrations\Hubspot\Requests\SearchCompanies;
use App\Models\ContactCluster;
use App\Models\Hub;
use App\Models\HubspotObject;
use App\Models\HubspotToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImportHubspotObject implements ShouldQueue
{
    use Queueable;

    protected Hub $hub;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected HubspotToken $token,
        protected ContactCluster $cluster,
    ) {
        $this->hub = $cluster->app->hub;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $hubspotCrmConnector = new CrmConnector(
            token: $this->token->token,
            hubspotToken: $this->token,
        );

        $searchCompanies = new SearchCompanies(
            filter: $this->cluster->filter,
        );
        $paginator = $hubspotCrmConnector->paginate($searchCompanies);
        $count = 0;
        $objectIds = [];
        foreach ($paginator->items() as $company) {
            $count++;

            $object = HubspotObject::query()
                ->updateOrCreate(
                    attributes: [
                        'hub_id' => $this->hub->id,
                        'type' => $this->cluster->type,
                        'hubspot_id' => $company['id'],
                    ],
                    values: [
                        'properties' => [
                            'name' => $company['properties']['name'],
                            'address' => $company['properties']['address'],
                            'city' => $company['properties']['city'],
                            'zip' => $company['properties']['zip'],
                            'country' => $company['properties']['country'],
                        ],
                    ]
                );

            $objectIds[] = $object->id;

            if ($object->location === null) {
                ResolveHubspotObjectCoordinates::dispatch($this->hub, $object);
            }
        }

        $this->cluster->objects()->sync($objectIds);
    }
}
