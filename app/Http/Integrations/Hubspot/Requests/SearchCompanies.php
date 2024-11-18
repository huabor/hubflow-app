<?php

namespace App\Http\Integrations\Hubspot\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\PaginationPlugin\Contracts\Paginatable;
use Saloon\Traits\Body\HasJsonBody;

class SearchCompanies extends Request implements HasBody, Paginatable
{
    use HasJsonBody;

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    public function __construct() {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/objects/companies/search';
    }

    protected function defaultBody(): array
    {
        $body = [
            'properties' => [
                'city',
                'country',
                'name',
                'zip',
                'address',
                'industry_sector',
                'hubspot_owner_id',
                'hs_all_owner_ids',
            ],
            'filterGroups' => [
                [
                    'filters' => [
                        [
                            'operator' => 'HAS_PROPERTY',
                            'propertyName' => 'address',
                        ],
                        [
                            'operator' => 'CONTAINS_TOKEN',
                            'propertyName' => 'zip',
                            'value' => '39*',
                        ],
                    ],
                ],
            ],
            'sorts' => [
                [
                    'propertyName' => 'hs_object_id',
                    'direction' => 'ASCENDING',
                ],
            ],
        ];

        return $body;
    }
}
