<?php

namespace App\Http\Integrations\Hubspot\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetCompany extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(
        protected string $companyId,
    ) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return "/objects/companies/$this->companyId";
    }

    protected function defaultQuery(): array
    {
        return [
            'archived' => 'false',
        ];
    }
}
