<?php

namespace App\Jobs;

use App\Enums\RefreshStatus;
use App\Http\Integrations\Hubspot\CrmConnector;
use App\Http\Integrations\Hubspot\Requests\SearchCompanies;
use App\Models\ContactCluster;
use App\Models\Hub;
use App\Models\HubspotObject;
use App\Models\HubspotToken;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;

class RefreshContactCluster implements ShouldQueue
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
        $this->cluster->refresh_status = RefreshStatus::NEW;

        if ($this->cluster->refresh_status === RefreshStatus::RUNNING) {
            $this->fail('Contact Cluster is currently refreshing');

            return;
        }

        $this->cluster->refresh_status = RefreshStatus::RUNNING;
        $this->cluster->save();

        $hubspotCrmConnector = new CrmConnector(
            token: $this->token->token,
            hubspotToken: $this->token,
        );

        $searchCompanies = new SearchCompanies(
            filter: $this->cluster->filter,
        );

        $paginator = $hubspotCrmConnector->paginate($searchCompanies);
        $count = 0;
        $batches = [];
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
                $batches[] = new ResolveHubspotObjectCoordinates(
                    hub: $this->hub,
                    hubspotObject: $object
                );
            }
        }

        $this->cluster->objects()->sync($objectIds);

        if (count($batches) > 0) {
            $cluster = $this->cluster;
            Bus::batch(
                $batches
            )
                ->finally(function (Batch $batch) use ($cluster) {
                    $cluster->refresh_status = RefreshStatus::DONE;
                    $cluster->save();
                })
                ->dispatch();
        } else {
            $this->cluster->refresh_status = RefreshStatus::DONE;
            $this->cluster->save();
        }
    }
}
