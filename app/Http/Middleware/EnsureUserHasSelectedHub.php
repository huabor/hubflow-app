<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasSelectedHub
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $hubs = $user->hubs;
        if ($hubs->count() === 1) {
            $user->hub_id = $hubs[0]->id;
            $user->save();
        }

        if ($user->selectedHub === null) {
            return redirect()->route('hubspot.token.index');
        }

        return $next($request);
    }
}
