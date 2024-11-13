<?php

use App\Http\Controllers\App\IndexController as AppIndexController;
use App\Http\Controllers\Hubspot\CallbackController as HubspotCallbackController;
use App\Http\Controllers\Hubspot\DeleteController as HubspotDeleteController;
use App\Http\Controllers\Hubspot\IndexController as HubspotIndexController;
use App\Http\Controllers\Hubspot\RedirectController as HubspotRedirectController;
use App\Http\Controllers\Profile\DeleteController as ProfileDeleteController;
use App\Http\Controllers\Profile\IndexController as ProfileIndexController;
use App\Http\Controllers\Profile\UpdateController as ProfileUpdateController;
use App\Models\HubspotCompany;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::get('/dashboard', function () {
    $companies = HubspotCompany::query()
        ->whereNotNull('coordinates')
        ->get();

    // dd($companies->groupBy('industry_sector'));
    return Inertia::render('Dashboard', [
        'companies' => $companies,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('apps', AppIndexController::class)->name('app.index');

    Route::get('profile', ProfileIndexController::class)->name('profile.edit');
    Route::patch('profile', ProfileUpdateController::class)->name('profile.update');
    Route::delete('profile', ProfileDeleteController::class)->name('profile.destroy');

    Route::get('hubspot', HubspotIndexController::class)->name('hubspot.token.index');
    Route::delete('hubspot/{token}', HubspotDeleteController::class)->name('hubspot.token.delete');

    Route::get('oauth/hubspot/redirect', HubspotRedirectController::class)->name('oauth.hubspot.redirect');
    Route::get('oauth/hubspot/callback', HubspotCallbackController::class)->name('oauth.hubspot.callback');
});

require __DIR__.'/auth.php';
