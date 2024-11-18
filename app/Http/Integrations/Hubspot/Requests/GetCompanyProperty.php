<?php

namespace App\Http\Integrations\Hubspot\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetCompanyProperty extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(
        protected ?string $property = null
    ) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        if ($this->property === null) {
            return '/properties/companies';
        }

        return "/properties/companies/{$this->property}";
    }
}
