<?php

namespace App\Http\Requests\App;

use App\Enums\AppType;
use App\Models\HubspotToken;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateBaseInformationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        return [
            'type' => [
                'required',
                'string',
                Rule::in(collect(AppType::TYPE_DEFINITION)->pluck('type')->values()),
            ],

            'hubspot_token_id' => [
                'required',
                Rule::exists(HubspotToken::getTableName(), 'id')
                    ->where(fn (QueryBuilder $query) => $query->where('user_id', $user->id)),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }
}
