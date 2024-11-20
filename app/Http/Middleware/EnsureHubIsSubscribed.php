<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureHubIsSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $hub = $request->user()->selectedHub;

        if ($hub && ! $hub->subscribed('default')) {
            // This user is not a paying customer...
            return redirect(route('billing.choose-subscription'));
        }

        return $next($request);
    }
}
