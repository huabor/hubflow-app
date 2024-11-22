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
        protected array $properties = [],
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

        $properties = array_merge(
            [
                'lastname',
                'firstname',
                'date_of_birth',

                'hubspot_owner_id',
                'associatedcompanyid',
            ],
            $this->properties,
        );


        $body = [
            'limit' => 200,
            'properties' => $properties,
            // 'filterGroups' => [
            //     [
            //         'filters' => [
            //             [
            //                 'operator' => 'HAS_PROPERTY',
            //                 'propertyName' => 'date_of_birth',
            //             ]
            //         ]
            //     ]
            // ]
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
