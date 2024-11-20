<?php

namespace App\Http\Controllers\Hubspot;

use App\Models\Hub;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class SelectHubController
{
    public function __invoke(Request $request, Hub $hub): RedirectResponse
    {
        $request->user()->hub_id = $hub->id;
        $request->user()->save();

        return to_route('app.index');
    }
}
