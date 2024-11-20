<?php

namespace App\Http\Middleware;

use App\Enums\AppType;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        $plans = [];
        foreach (config('cashier_plans.plans') as $key => $plan) {
            $plans[$key] = [
                'key' => $key,
                'amount' => (int) $plan['amount']['value'],
                'currency' => $plan['amount']['currency'],
                ...$plan['marketing'],
            ];
        }

        $subscription = $user?->selectedHub?->subscription('default');

        $planDetails = [
            'enabled_apps' => [
                AppType::CONTACT_CLUSTER,
            ],

            'maximum_locations' => 100,
        ];

        if ($subscription !== null) {
            $planDetails['enabled_apps'][] = AppType::BIRTHDAY_REMINDER;
        }

        return [
            ...parent::share($request),

            'system' => [
                'plans' => $plans,
            ],

            'auth' => [
                'user' => $user,
                'subscription' => $subscription,
                'on_grace_period' => $subscription?->onGracePeriod(),
                'plan_details' => $planDetails,
            ],

            'flash' => [
                'action' => fn () => $request->session()->get('action'),
                'notification' => fn () => $request->session()->get('notification'),
            ],
        ];
    }
}
