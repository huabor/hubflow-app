<?php

namespace App\Http\Controllers\App;

use App\Enums\AppType;
use App\Models\App;
use App\Models\HubspotCompany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class CrmCardController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(Request $request, App $app): Response|RedirectResponse|Collection
    {
        $renderProps = [
            'app' => $app,
            'token' => $request->token,
        ];

        if (isset($request->company)) {
            $company = HubspotCompany::query()->find($request->company);

            if ($company !== null) {
                $renderProps['company'] = $company;
            }
        }

        $view = match ($app->type) {
            AppType::CONTACT_CLUSTER => 'App/ContactCluster/CrmCard',
            default => null,
        };

        if ($view === null) {
            return to_route('app.index')->with('notification', [
                'type' => 'warning',
                'message' => 'Please try again!',
            ]);
        }

        return Inertia::render($view, $renderProps);
    }
}
