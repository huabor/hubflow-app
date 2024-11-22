<?php

namespace App\Http\Integrations\Hubspot\Requests;

use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Contracts\Driver;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;

class GetOwner extends Request implements Cacheable
{
    use HasCaching;

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(
        protected string|int $userId,
        protected string $idProperty = 'id'
    ) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return "/owners/$this->userId";
    }

    protected function defaultQuery(): array
    {
        return [
            'idProperty' => $this->idProperty,
            'archived' => 'false',
        ];
    }

    public function resolveCacheDriver(): Driver
    {
        return new LaravelCacheDriver(Cache::store('database'));
    }

    public function cacheExpiryInSeconds(): int
    {
        return 3600;
    }

    protected function cacheKey(PendingRequest $pendingRequest): ?string
    {
        return "owner-$this->idProperty-$this->userId";
    }
}
