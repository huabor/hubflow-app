<?php

namespace App\Http\Integrations\Hubspot\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetOwner extends Request
{
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
}
