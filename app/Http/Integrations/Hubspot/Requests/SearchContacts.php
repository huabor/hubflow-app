<?php

namespace App\Http\Integrations\Hubspot\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class SearchContacts extends Request implements HasBody
{
    use HasJsonBody;

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    public function __construct(
        protected array $days,
        protected array $months,
    ) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/objects/contacts/search';
    }

    protected function defaultBody(): array
    {
        return [
            'limit' => 200,
            'properties' => [
                'lastname',
                'firstname',
                'date_of_birth',
                'birthday__day',
                'birthday__month',
                'birthdaytext',
                'hubspot_owner_id',
                'associatedcompanyid',
            ],
            'filterGroups' => [
                [
                    'filters' => [
                        [
                            'operator' => 'IN',
                            'propertyName' => 'birthday__day',
                            'values' => $this->days,
                        ],
                        [
                            'operator' => 'IN',
                            'propertyName' => 'birthday__month',
                            'values' => $this->months,
                        ],
                    ],
                ],
            ],
        ];
    }
}
