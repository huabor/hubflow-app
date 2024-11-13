<?php

namespace App\Http\Middleware;

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
        $plans = [];
        foreach (config('cashier_plans.plans') as $key => $plan) {
            $plans[$key] = [
                'key' => $key,
                'amount' => (int) $plan['amount']['value'],
                'currency' => $plan['amount']['currency'],
                ...$plan['marketing'],
            ];
        }

        $subscription = $request->user()?->subscription('default');

        return [
            ...parent::share($request),

            'system' => [
                'plans' => $plans,
            ],

            'auth' => [
                'user' => $request->user(),
                'subscription' => $subscription,
                'on_grace_period' => $subscription?->onGracePeriod(),
            ],

            'flash' => [
                'notification' => fn () => $request->session()->get('notification'),
            ],
        ];
    }
}
