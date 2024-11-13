<?php

namespace App\Http\Integrations\Hubspot;

use Saloon\Helpers\OAuth2\OAuthConfig;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Traits\OAuth2\AuthorizationCodeGrant;
use Saloon\Traits\Plugins\AcceptsJson;

class CrmConnector extends Connector
{
    use AcceptsJson;
    use AuthorizationCodeGrant;

    public function __construct(
        public readonly string $token
    ) {}

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://api.hubapi.com/crm/v3/';
    }

    protected function defaultOauthConfig(): OAuthConfig
    {
        return OAuthConfig::make()
            ->setClientId('ac502a50-3c82-4604-b864-d9b3dcd0b72e')
            ->setClientSecret('9ea1438c-8645-4faf-ae81-584da420aee2')
            ->setDefaultScopes([
                'crm.objects.companies.read',
                'crm.objects.contacts.read',
                'crm.objects.owners.read',
            ])
            ->setRedirectUri('http://localhost/auth/callback')
            ->setAuthorizeEndpoint('https://app.hubspot.com/oauth/authorize')
            ->setTokenEndpoint('https://api.hubapi.com/oauth/v1/token')
            ->setUserEndpoint('/me')
            ->setRequestModifier(function (Request $request) {
                // Optional: Modify the requests being sent.
            });
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->token);
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [];
    }

    /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return [];
    }
}
