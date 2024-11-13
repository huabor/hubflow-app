<?php

use App\Http\Controllers\App\IndexController as AppIndexController;
use App\Http\Controllers\Billing\CancelSubscriptionController as BillingCancelSubscriptionController;
use App\Http\Controllers\Billing\ChooseSubscriptionController as BillingChooseSubscriptionController;
use App\Http\Controllers\Billing\CreateController as BillingCreateController;
use App\Http\Controllers\Billing\DownloadInvoiceController as BillingDownloadInvoiceController;
use App\Http\Controllers\Billing\IndexController as BillingIndexController;
use App\Http\Controllers\Billing\ResumeSubscriptionController as BillingResumeSubscriptionController;
use App\Http\Controllers\Billing\SwitchPlanController as BillingSwitchPlanController;
use App\Http\Controllers\Billing\UpdateController as BillingUpdateController;
use App\Http\Controllers\Billing\UpdatePaymentMethodController as BillingUpdatePaymentMethodController;
use App\Http\Controllers\Hubspot\CallbackController as HubspotCallbackController;
use App\Http\Controllers\Hubspot\DeleteController as HubspotDeleteController;
use App\Http\Controllers\Hubspot\IndexController as HubspotIndexController;
use App\Http\Controllers\Hubspot\RedirectController as HubspotRedirectController;
use App\Http\Controllers\Profile\DeleteController as ProfileDeleteController;
use App\Http\Controllers\Profile\IndexController as ProfileIndexController;
use App\Http\Controllers\Profile\UpdateController as ProfileUpdateController;
use App\Http\Middleware\EnsureUserIsSubscribed;
use App\Models\HubspotCompany;
use App\Models\User;
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
    //     $user = User::find(1);
    //     $order = $user->orders()->first();

    // $invoice = $user->findInvoice($order->number);
    // return $invoice->view();
    // dd($invoice);

    $companies = HubspotCompany::query()
        ->whereNotNull('coordinates')
        ->get();

    // dd($companies->groupBy('industry_sector'));
    return Inertia::render('Dashboard', [
        'companies' => $companies,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware([
    'auth',
    'verified',
    EnsureUserIsSubscribed::class,
])
    ->group(function () {
        Route::get('billing', BillingIndexController::class)->name('billing.index');
        Route::patch('billing', BillingUpdateController::class)->name('billing.update');
        Route::get('billing/download-invoice/{order}', BillingDownloadInvoiceController::class)->name('billing.download-invoice');

        Route::get('billing/update-payment-method', BillingUpdatePaymentMethodController::class)->name('billing.update-payment-method');

        Route::get('apps', AppIndexController::class)->name('app.index');

        Route::get('hubspot', HubspotIndexController::class)->name('hubspot.token.index');
        Route::delete('hubspot/{token}', HubspotDeleteController::class)->name('hubspot.token.delete');

        Route::get('oauth/hubspot/redirect', HubspotRedirectController::class)->name('oauth.hubspot.redirect');
        Route::get('oauth/hubspot/callback', HubspotCallbackController::class)->name('oauth.hubspot.callback');
    });

Route::middleware('auth')->group(function () {
    Route::get('billing/subscribe', BillingChooseSubscriptionController::class)->name('billing.choose-subscription');
    Route::get('billing/subscribe/{plan}', BillingCreateController::class)->name('billing.subscribe');
    Route::get('billing/subscribe/switch/{plan}', BillingSwitchPlanController::class)->name('billing.switch-plan');
    Route::patch('billing/resume', BillingResumeSubscriptionController::class)->name('billing.resume');
    Route::delete('billing/cancel', BillingCancelSubscriptionController::class)->name('billing.cancel');

    Route::get('profile', ProfileIndexController::class)->name('profile.edit');
    Route::patch('profile', ProfileUpdateController::class)->name('profile.update');
    Route::delete('profile', ProfileDeleteController::class)->name('profile.destroy');
});

require __DIR__.'/auth.php';
