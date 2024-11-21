<?php

namespace App\Http\Controllers\App\ContactCluster;

use App\Http\Requests\App\ContactCluster\StoreRequest;
use App\Models\App;
use App\Models\ContactCluster;
use Illuminate\Http\RedirectResponse;

final class StoreController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(StoreRequest $request, App $app): RedirectResponse
    {
        $contactCluster = ContactCluster::query()
            ->find($request->validated('id'));

        if ($contactCluster !== null) {
            $contactCluster->update($request->validated());
        } else {
            $contactCluster = new ContactCluster;
            $contactCluster->app_id = $app->id;
            $contactCluster->fill($request->validated());
            $contactCluster->save();
        }

        $message = $contactCluster->wasRecentlyCreated ?
            "$contactCluster->name successfully created!'" :
            "$contactCluster->name successfully updated!'";

        return to_route('app.show', $app->type)->with('notification', [
            'type' => 'success',
            'message' => $message,
        ]);
    }
}
