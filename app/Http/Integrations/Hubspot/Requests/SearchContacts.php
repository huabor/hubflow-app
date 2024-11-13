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
        protected string $field,
        protected string $operator,
        protected mixed $filterValue,
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
        $valueKey = 'value';
        if (in_array($this->operator, ['IN'])) {
            $valueKey = 'values';
        }

        $body = [
            'limit' => 200,
            'properties' => [
                'lastname',
                'firstname',
                'star_sign',
                'date_of_birth',
                'birthdaytext',

                'hubspot_owner_id',
                'associatedcompanyid',
            ],
            'filterGroups' => [
                [
                    'filters' => [
                        [
                            'operator' => $this->operator,
                            'propertyName' => $this->field,
                            $valueKey => $this->filterValue,
                        ],
                    ],
                ],
            ],
        ];

        return $body;
    }
}
