<?php

use App\Enums\AppType;
use App\Http\Controllers\App\ContactCluster\AppController as ContactClusterAppController;
use App\Http\Controllers\App\CreateController as AppCreateController;
use App\Http\Controllers\App\CrmCardController as AppCrmCardController;
use App\Http\Controllers\App\IndexController as AppIndexController;
use App\Http\Controllers\App\ShowController as AppShowController;
use App\Http\Controllers\App\StoreController as AppStoreController;
use App\Http\Controllers\App\ValidateBaseInformationController as AppValidateBaseInformationController;
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
use App\Http\Controllers\Hubspot\CallbackController as HubspotCallbackController;
use App\Http\Controllers\Hubspot\DeleteController as HubspotDeleteController;
use App\Http\Controllers\Hubspot\IndexController as HubspotIndexController;
use App\Http\Controllers\Hubspot\RedirectController as HubspotRedirectController;
use App\Http\Controllers\Profile\DeleteController as ProfileDeleteController;
use App\Http\Controllers\Profile\IndexController as ProfileIndexController;
use App\Http\Controllers\Profile\UpdateController as ProfileUpdateController;
use App\Http\Middleware\EnsureUserIsSubscribed;
use App\Models\HubspotToken;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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

        Route::get('app', AppIndexController::class)->name('app.index');
        Route::get('app/create', fn () => Redirect::route('app.index'));
        Route::post('app', AppStoreController::class)->name('app.store');
        Route::post('app/validate-base-information', AppValidateBaseInformationController::class)->name('app.validate-base-information');
        Route::get('app/create/{type}', AppCreateController::class)->name('app.create');
        Route::get('app/{app}', AppShowController::class)->name('app.show');

        Route::get('app/{app}/contact-cluster', ContactClusterAppController::class)->name('app.contact-cluster');

        Route::get('hubspot', HubspotIndexController::class)->name('hubspot.token.index');
        Route::delete('hubspot/{token}', HubspotDeleteController::class)->name('hubspot.token.delete');
        Route::get('hubspot/api/{token}/company-property', HubspotApiCompanyPropertyController::class)->name('hubspot.api.company-property');

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

Route::get('/hubspot/crm-card/contact-cluster', function (Request $request) {
    $hubspotUserId = $request->get('userId');
    $hubspotCompanyId = $request->get('associatedObjectId');

    $hubspotToken = HubspotToken::query()
        ->where('hubspot_user_id', $hubspotUserId)
        ->firstOrFail();

    $hubspotCompany = $hubspotToken->hubspotCompanies()
        ->where('hubspot_id', $hubspotCompanyId)
        ->firstOrFail();

    $user = $hubspotToken->user;

    $app = $user->apps()
        ->where('type', AppType::CONTACT_CLUSTER)
        ->firstOrFail();

    $token = $user->createToken(
        name: 'crm-card',
        abilities: ['crm-card'],
        expiresAt: now()->addHours(6)
    );

    $appUrl = route('app.show', [
        'app' => $app,
    ]);

    $iframeUrl = route('crm-card.show', [
        'app' => $app,
        'token' => $token->plainTextToken,
        'company' => $hubspotCompany->id,
    ]);

    $result = [
        'primaryAction' => [
            'type' => 'IFRAME',
            'width' => 1920,
            'height' => 1080,
            'uri' => $iframeUrl,
            'label' => 'Show Contact Cluster',
            'associatedObjectProperties' => [],
        ],

        // 'secondaryActions' => [[
        //     'type' => 'IFRAME',
        //     'width' => 890,
        //     'height' => 748,
        //     'uri' => $iframeUrl,
        //     'label' => 'Show Contact Cluster',
        //     'associatedObjectProperties' => [],
        // ]],

        'results' => [
            [
                'objectId' => $app->id,
                'title' => "Contact Cluster - $hubspotCompany->name",
                'description' => "Show $hubspotCompany->name and surrounding companies on the world map.",
                'link' => $appUrl,
            ],
        ],
    ];

    return response()->json($result);
});

Route::middleware([
    'verified',
    'auth:sanctum',
    'abilities:crm-card',
])
    ->group(function () {
        Route::get('hubspot/crm-card/{app}', AppCrmCardController::class)->name('crm-card.show');
        Route::get('hubspot/crm-card/{app}/contact-cluster', ContactClusterAppController::class)->name('crm-card.contact-cluster');
    });

require __DIR__.'/auth.php';
