<?php

namespace App\Http\Integrations\Hubspot;

use App\Models\HubspotToken;
use GuzzleHttp\Exception\ClientException;
use Laravel\Socialite\Facades\Socialite;
use Saloon\Contracts\Body\HasBody;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Helpers\OAuth2\OAuthConfig;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\PaginationPlugin\Paginator;
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\RateLimitPlugin\Limit;
use Saloon\RateLimitPlugin\Stores\MemoryStore;
use Saloon\RateLimitPlugin\Traits\HasRateLimits;
use Saloon\Traits\OAuth2\AuthorizationCodeGrant;
use Saloon\Traits\Plugins\AcceptsJson;

class CrmConnector extends Connector implements HasPagination
{
    public ?int $tries = 3;

    public ?bool $throwOnMaxTries = false;

    use AcceptsJson;
    use AuthorizationCodeGrant;
    use HasRateLimits;

    public function __construct(
        public readonly string $token,
        public readonly ?HubspotToken $hubspotToken = null,
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

    public function paginate(Request $request): Paginator
    {
        return new class(connector: $this, request: $request) extends Paginator
        {
            protected ?int $perPageLimit = 200;

            protected ?string $lastId = null;

            protected function isLastPage(Response $response): bool
            {
                return $response->collect('results')->count() === 0;
            }

            protected function getPageItems(Response $response, Request $request): array
            {
                if ($response->collect('results')->count() > 0) {
                    $this->lastId = $response->collect('results')->last()['id'];
                }

                return $response->json('results');
            }

            protected function applyPagination(Request $request): Request
            {
                if (isset(class_implements($request)[HasBody::class])) {
                    $body = $request->body()->all();
                    $body['limit'] = $this->perPageLimit;

                    if ($this->lastId !== null) {
                        $body['filterGroups'][0]['filters'][] = [
                            'operator' => 'GT',
                            'propertyName' => 'hs_object_id',
                            'value' => $this->lastId,
                        ];
                    }

                    $request->body()->set($body);
                }

                return $request;
            }
        };
    }

    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        if ($exception instanceof RequestException && $exception->getResponse()->status() === 401 && $this->hubspotToken !== null) {
            /** @var \SocialiteProviders\HubSpot $provider */
            $provider = Socialite::driver('hubspot');
            try {
                $refreshToken = $provider->refreshToken($this->hubspotToken->refresh_token);
            } catch (ClientException $exception) {
                return true;
            }

            $this->hubspotToken->token = $refreshToken['access_token'];
            $this->hubspotToken->save();

            $request->authenticate(new TokenAuthenticator($this->hubspotToken->token));
        }

        return true;
    }

    protected function resolveLimits(): array
    {
        return [
            Limit::allow(5)->everySeconds(1)->sleep(),
        ];
    }

    protected function resolveRateLimitStore(): RateLimitStore
    {
        return new MemoryStore;
    }
}
