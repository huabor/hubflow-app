<?php

namespace App\Http\Controllers\App\ContactCluster;

use App\Jobs\ImportHubspotObject;
use App\Models\App;
use App\Models\ContactCluster;
use App\Models\HubspotToken;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class RefreshController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(Request $request, App $app, ContactCluster $cluster): RedirectResponse
    {
        $user = $request->user();
        $hub = $user->selectedHub;

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

        ImportHubspotObject::dispatch($token, $cluster);

        $cluster->refreshed_at = Carbon::now();
        $cluster->save();

        return to_route('app.show', $app->type)->with('notification', [
            'type' => 'success',
            'message' => "$cluster->name successfully refreshed.",
        ]);
    }
}
