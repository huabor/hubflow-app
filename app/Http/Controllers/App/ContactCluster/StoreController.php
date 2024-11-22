<?php

namespace App\Http\Controllers\App\ContactCluster;

use App\Enums\RefreshStatus;
use App\Http\Requests\App\ContactCluster\StoreRequest;
use App\Jobs\RefreshContactCluster;
use App\Models\App;
use App\Models\ContactCluster;
use App\Models\HubspotToken;
use Illuminate\Http\RedirectResponse;

final class StoreController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(StoreRequest $request, App $app): RedirectResponse
    {
        $user = $request->user();
        $hub = $user->selectedHub;

        $cluster = ContactCluster::query()
            ->find($request->validated('id'));

        if ($cluster !== null) {
            $cluster->update($request->validated());
        } else {
            $cluster = new ContactCluster;
            $cluster->app_id = $app->id;
            $cluster->fill($request->validated());
            $cluster->save();
        }

        $token = HubspotToken::query()
            ->where(
                column: 'user_id',
                operator: '=',
                value: $user->id
            )
            ->where(
                column: 'hub_id',
                operator: '=',
                value: $hub->id
            )
            ->firstOrFail();

        $cluster->refresh_status = RefreshStatus::QUEUED->value;
        $cluster->save();
        RefreshContactCluster::dispatch($token, $cluster);

        $message = $cluster->wasRecentlyCreated ?
            "$cluster->name successfully created!'" :
            "$cluster->name successfully updated!'";

        $route = 'app.show';
        if ($request->has('crm_card')) {
            $route = 'crm-card.show';
        }

        return to_route($route, $app->type)->with('notification', [
            'type' => 'success',
            'message' => $message,
        ]);
    }
}
