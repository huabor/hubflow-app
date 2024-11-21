<?php

namespace App\Http\Requests\App\ContactCluster;

use App\Enums\Hubspot\ObjectType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => [
                'sometimes',
            ],

            'type' => [
                'required',
                'numeric',
                Rule::in(ObjectType::values()),
            ],

            'name' => [
                'required',
                'string',
            ],

            'color' => [
                'required',
                'hex_color',
            ],

            'filter' => [
                'array',
                'min:1',
            ],

            'filter.*.propertyName' => [
                'required',
                'string',
            ],

            'filter.*.operator' => [
                'required',
                'string',
                Rule::in(['EQ', 'NEQ', 'HAS_PROPERTY', 'NOT_HAS_PROPERTY', 'CONTAINS_TOKEN', 'NOT_CONTAINS_TOKEN']),
            ],

            'filter.*.value' => [
                'required_if:filter.*.operator:EQ,NEQ',
                'string',
            ],
        ];
    }
}
