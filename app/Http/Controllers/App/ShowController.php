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

final class ShowController
{
    /**
     * Display the user's profile form.
     */
    public function __invoke(Request $request, AppType $type): Response|RedirectResponse|Collection
    {
        $user = $request->user();
        $hub = $user->selectedHub;

        $appType = AppType::TYPE_DEFINITION[$type->value];

        $app = $hub->apps()
            ->where(
                column: 'type',
                operator: '=',
                value: $type
            )
            ->first();

        if ($app === null) {
            $app = new App;
            $app->hub_id = $hub->id;
            $app->type = $type;
            $app->name = $appType['name'];
            $app->save();
        }

        $renderProps = [
            'app' => $app,
        ];

        $view = match ($app->type) {
            AppType::BIRTHDAY_REMINDER => 'App/BirthdayReminder/App',
            AppType::CONTACT_CLUSTER => 'App/ContactCluster/App',
            default => null,
        };

        if ($app->type === AppType::CONTACT_CLUSTER) {
            if (isset($request->company)) {
                $company = HubspotCompany::query()->find($request->company);
                $company->coordinates = [
                    'x' => $company->location->getX(),
                    'y' => $company->location->getY(),
                ];

                if ($company !== null) {
                    $renderProps['company'] = $company;
                }
            }
        }

        if ($view === null) {
            return to_route('app.index')->with('notification', [
                'type' => 'warning',
                'message' => 'Please try again!',
            ]);
        }

        return Inertia::render($view, $renderProps);
    }
}
