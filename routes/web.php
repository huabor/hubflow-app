<?php

use App\Http\Controllers\App\ContactCluster\AppController as ContactClusterAppController;
use App\Http\Controllers\App\ContactCluster\CrmCardController;
use App\Http\Controllers\App\ContactCluster\RefreshController as ContactClusterRefreshController;
use App\Http\Controllers\App\ContactCluster\StoreController as ContactClusterStoreController;
use App\Http\Controllers\App\IndexController as AppIndexController;
use App\Http\Controllers\App\ShowController as AppShowController;
use App\Http\Controllers\Billing\CancelSubscriptionController as BillingCancelSubscriptionController;
use App\Http\Controllers\Billing\ChooseSubscriptionController as BillingChooseSubscriptionController;
use App\Http\Controllers\Billing\CreateController as BillingCreateController;
use App\Http\Controllers\Billing\DownloadInvoiceController as BillingDownloadInvoiceController;
use App\Http\Controllers\Billing\IndexController as BillingIndexController;
use App\Http\Controllers\Billing\ResumeSubscriptionController as BillingResumeSubscriptionController;
use App\Http\Controllers\Billing\SwitchPlanController as BillingSwitchPlanController;
use App\Http\Controllers\Billing\UpdateController as BillingUpdateController;
use App\Http\Controllers\Billing\UpdatePaymentMethodController as BillingUpdatePaymentMethodController;
use App\Http\Controllers\Hubspot\API\CompanyPropertyController as HubspotApiCompanyPropertyController;
use App\Http\Controllers\Hubspot\API\CompanySearchController as HubspotApiCompanySearchController;
use App\Http\Controllers\Hubspot\CallbackController as HubspotCallbackController;
use App\Http\Controllers\Hubspot\DeleteController as HubspotDeleteController;
use App\Http\Controllers\Hubspot\IndexController as HubspotIndexController;
use App\Http\Controllers\Hubspot\RedirectController as HubspotRedirectController;
use App\Http\Controllers\Hubspot\SelectHubController as HubspotSelectHubController;
use App\Http\Controllers\Profile\DeleteController as ProfileDeleteController;
use App\Http\Controllers\Profile\IndexController as ProfileIndexController;
use App\Http\Middleware\EnsureHubIsSubscribed;
use App\Http\Middleware\EnsureUserHasSelectedHub;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Home'))->name('home');
Route::get('/tos', fn () => Inertia::render('Home'))->name('tos');

Route::get('oauth/hubspot/redirect', HubspotRedirectController::class)->name('oauth.hubspot.redirect');
Route::get('oauth/hubspot/callback', HubspotCallbackController::class)->name('oauth.hubspot.callback');

Route::get('/hubspot/crm-card/contact-cluster', CrmCardController::class);

Route::middleware('auth')->group(function () {
    Route::get('hub', HubspotIndexController::class)->name('hubspot.token.index');
    Route::post('hub/select/{hub}', HubspotSelectHubController::class)->name('hubspot.select-hub');
    Route::delete('hub/{token}', HubspotDeleteController::class)->name('hubspot.token.delete');

    Route::get('profile', ProfileIndexController::class)->name('profile.edit');
    Route::delete('profile', ProfileDeleteController::class)->name('profile.destroy');
});

Route::middleware([
    'auth',
    'verified',
    EnsureUserHasSelectedHub::class,
])
    ->group(function () {
        Route::get('billing', BillingIndexController::class)->name('billing.index');
        Route::patch('billing', BillingUpdateController::class)->name('billing.update');
        Route::get('billing/download-invoice/{order}', BillingDownloadInvoiceController::class)->name('billing.download-invoice');

        Route::get('billing/subscribe', BillingChooseSubscriptionController::class)->name('billing.choose-subscription');
        Route::get('billing/subscribe/{plan}', BillingCreateController::class)->name('billing.subscribe');
        Route::get('billing/subscribe/switch/{plan}', BillingSwitchPlanController::class)->name('billing.switch-plan');
        Route::patch('billing/resume', BillingResumeSubscriptionController::class)->name('billing.resume');
        Route::delete('billing/cancel', BillingCancelSubscriptionController::class)->name('billing.cancel');
    });

Route::middleware([
    'auth',
    'verified',
    EnsureUserHasSelectedHub::class,
    // EnsureHubIsSubscribed::class,
])
    ->group(function () {
        Route::get('billing/update-payment-method', BillingUpdatePaymentMethodController::class)->name('billing.update-payment-method');

        Route::get('app', AppIndexController::class)->name('app.index');
        Route::get('app/{type}', AppShowController::class)->name('app.show');

        Route::get('contact-cluster/{cluster}', ContactClusterAppController::class)->name('app.contact-cluster');
        Route::post('app/{app}/contact-cluster', ContactClusterStoreController::class)->name('app.contact-cluster.store');
        Route::get('app/{app}/contact-cluster/{cluster}', ContactClusterRefreshController::class)->name('app.contact-cluster.refresh');

        Route::get('hubspot/api/company-property', HubspotApiCompanyPropertyController::class)->name('hubspot.api.company-property');
        Route::post('hubspot/api/company-search', HubspotApiCompanySearchController::class)->name('hubspot.api.company-search');
    });

Route::middleware([
    'auth:sanctum',
    'abilities:crm-card',
])
    ->group(function () {
        Route::get('hubspot/crm-card/{type}', AppShowController::class)->name('crm-card.show');
    });

require __DIR__.'/auth.php';
